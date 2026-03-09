<?php

namespace App\Services;

use App\Models\BorrowTransaction;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Jobs\SendReminderEmail;
use App\Mail\BorrowNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BorrowService
{
    public function __construct(
        private InventoryService $inventoryService,
        private GoogleCalendarService $calendarService,
        private AiSuggestionService $aiService,
    ) {}

    public function createBorrow(array $data): BorrowTransaction
    {
        return \DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                $inventory = Inventory::findOrFail($item['inventory_id']);
                if ($inventory->stock_available < $item['quantity']) {
                    throw new \Exception(__('borrow.insufficient_stock', ['available' => "{$inventory->stock_available} ({$inventory->name})"]));
                }
            }

            $borrow = BorrowTransaction::create([
                'project_id'           => $data['project_id'],
                'expected_return_date' => $data['expected_return_date'],
                'notes'                => $data['notes'] ?? null,
                'code'                 => $this->generateCode(),
                'requested_by'         => auth()->id(),
                'borrow_date'          => now()->toDateString(),
                'status'               => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $borrow->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'quantity'     => $item['quantity'],
                ]);
            }

            // Send created notification to requester
            try {
                $borrow->load(['project', 'items.inventory', 'requester']);
                Mail::to($borrow->requester->email)
                    ->send(new BorrowNotificationMail($borrow, 'created'));
            } catch (\Exception $e) {
                logger()->warning('Borrow created notification failed', ['error' => $e->getMessage()]);
            }

            return $borrow;
        });
    }

    public function approveBorrow(BorrowTransaction $borrow): BorrowTransaction
    {
        return \DB::transaction(function () use ($borrow) {
            foreach ($borrow->items as $item) {
                $inventory = $item->inventory;
                $this->inventoryService->decreaseStock($inventory, $item->quantity);

                $this->inventoryService->recordHistory(
                    $inventory,
                    auth()->id(),
                    'borrowed',
                    $inventory->stock_available + $item->quantity,
                    -$item->quantity,
                    "Borrowed for project: {$borrow->project->name}",
                    BorrowTransaction::class,
                    $borrow->id,
                );
            }

            $borrow->update([
                'status'      => 'borrowed',
                'approved_by' => auth()->id(),
                'borrow_date' => now(),
            ]);

            // Create Google Calendar event (summary of items)
            try {
                $itemNames = $borrow->items->map(fn($i) => $i->inventory->name)->join(', ');
                $displayTitle = \Str::limit($itemNames, 50);
                $eventId = $this->calendarService->createEvent(
                    $borrow->requester,
                    "[Return] {$displayTitle} - {$borrow->project->name}",
                    "Please return borrowed items for project: {$borrow->project->name}",
                    $borrow->expected_return_date->toDateString(),
                );
                $borrow->update(['google_calendar_event_id' => $eventId]);
            } catch (\Exception $e) {
                logger()->warning('Google Calendar event creation failed', ['error' => $e->getMessage()]);
            }

            // Send approval notification to requester
            try {
                $borrow->load(['project', 'items.inventory', 'requester']);
                Mail::to($borrow->requester->email)
                    ->send(new BorrowNotificationMail($borrow->fresh(), 'approved'));
            } catch (\Exception $e) {
                logger()->warning('Borrow approval notification failed', ['error' => $e->getMessage()]);
            }

            // Queue reminder email H-1
            SendReminderEmail::dispatch($borrow, 'reminder_h1')
                ->delay(now()->diffInSeconds($borrow->expected_return_date->subDay()));

            // Trigger AI synchronization analysis
            $this->aiService->runGlobalAnalysis();

            return $borrow->fresh();
        });
    }

    public function rejectBorrow(BorrowTransaction $borrow, string $reason = null): BorrowTransaction
    {
        $borrow->update([
            'status' => 'rejected',
            'notes'  => $reason,
        ]);

        return $borrow->fresh();
    }

    private function generateCode(): string
    {
        return 'BRW-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
    }
}
