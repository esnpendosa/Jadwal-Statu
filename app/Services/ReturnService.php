<?php

namespace App\Services;

use App\Models\BorrowTransaction;
use App\Models\ReturnTransaction;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class ReturnService
{
    public function __construct(
        private InventoryService $inventoryService,
        private RiskScoreService $riskScoreService,
    ) {}

    public function processReturn(BorrowTransaction $borrow, array $data): ReturnTransaction
    {
        return \DB::transaction(function () use ($borrow, $data) {
            $isLate = now()->toDateString() > $borrow->expected_return_date->toDateString();
            $daysLate = $isLate ? now()->diffInDays($borrow->expected_return_date) : 0;

            $quantityGood    = $data['quantity_good'] ?? 0;
            $quantityPoor    = $data['quantity_poor'] ?? 0;
            $quantityDamaged = $data['quantity_damaged'] ?? 0;
            $quantityLost    = $data['quantity_lost'] ?? 0;
            $quantityReturned = $quantityGood + $quantityPoor + $quantityDamaged + $quantityLost;

            $item = \App\Models\BorrowItem::findOrFail($data['borrow_item_id']);
            $inventory = $item->inventory;

            // Create return record
            $return = ReturnTransaction::create([
                'code'                  => 'RET-' . strtoupper(\Str::random(8)),
                'borrow_transaction_id' => $borrow->id,
                'borrow_item_id'        => $item->id,
                'returned_by'           => auth()->id(),
                'quantity_returned'     => $quantityReturned,
                'quantity_good'         => $quantityGood,
                'quantity_poor'         => $quantityPoor,
                'quantity_damaged'      => $quantityDamaged,
                'quantity_lost'         => $quantityLost,
                'condition_notes'       => $data['condition_notes'] ?? null,
                'is_late'               => $isLate,
                'days_late'             => $daysLate,
                'notes'                 => $data['notes'] ?? null,
                'status'                => 'pending',
            ]);

            // Update borrow item status
            $item->increment('quantity_returned', $quantityReturned);

            // Update inventory stock
            $this->inventoryService->returnStock($inventory, $quantityGood, $quantityPoor, $quantityDamaged, $quantityLost);

            $this->inventoryService->recordHistory(
                $inventory,
                auth()->id(),
                'returned',
                $inventory->stock_available - ($quantityGood + $quantityPoor),
                ($quantityGood + $quantityPoor),
                "Returned from project: {$borrow->project->name}",
                ReturnTransaction::class,
                $return->id,
            );

            // Check if ALL items in the borrow transaction are fully returned
            $totalRemaining = $borrow->items->sum(function($i) {
                return $i->quantity - $i->quantity_returned;
            });

            if ($totalRemaining <= 0) {
                $borrow->update([
                    'status'             => 'completed',
                    'actual_return_date' => now(),
                ]);
            }

            // Calculate risk scores
            $this->riskScoreService->evaluateReturn($return, $borrow);

            return $return;
        });
    }

    public function verifyReturn(ReturnTransaction $return): ReturnTransaction
    {
        $return->update([
            'status'      => 'verified',
            'received_by' => auth()->id(),
        ]);
        return $return;
    }
}
