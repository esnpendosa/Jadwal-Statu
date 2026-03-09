<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Support\Str;

class InventoryService
{
    public function __construct(
        private AiSuggestionService $aiService,
    ) {}

    public function create(array $data): Inventory
    {
        $data['code'] = $data['code'] ?? $this->generateCode($data['name']);
        $data['stock_available'] = $data['stock_total'];
        $data['created_by'] = auth()->id();

        $inventory = Inventory::create($data);

        $this->recordHistory($inventory, auth()->id(), 'added', 0, $data['stock_total']);

        return $inventory;
    }

    public function update(Inventory $inventory, array $data): Inventory
    {
        // Delete old image if a new one was uploaded
        if (isset($data['image']) && $inventory->image) {
            \Storage::disk('public')->delete($inventory->image);
        }

        $oldStock = $inventory->stock_total;
        $inventory->update($data);

        if (isset($data['stock_total']) && $data['stock_total'] != $oldStock) {
            $diff = $data['stock_total'] - $oldStock;
            $inventory->stock_available = max(0, $inventory->stock_available + $diff);
            $inventory->save();
            $this->recordHistory($inventory, auth()->id(), 'adjusted', $oldStock, $diff);
        }

        $inventory = $inventory->fresh();
        $this->aiService->analyzeSyncInventory($inventory);

        return $inventory;
    }

    public function decreaseStock(Inventory $inventory, int $quantity): void
    {
        if ($inventory->stock_available < $quantity) {
            throw new \Exception(__('inventory.insufficient_stock'));
        }

        $before = $inventory->stock_available;
        $inventory->decrement('stock_available', $quantity);
        $inventory->increment('stock_borrowed', $quantity);
    }

    public function returnStock(Inventory $inventory, int $good, int $poor, int $damaged, int $lost): void
    {
        $totalReturned = $good + $poor + $damaged + $lost;

        // Reload to get fresh values and prevent race conditions
        $inventory->refresh();

        // Prevent UNSIGNED BIGINT underflow — never go below 0
        $newBorrowed  = max(0, $inventory->stock_borrowed - $totalReturned);
        $newAvailable = $inventory->stock_available + $good + $poor;
        $newTotal     = $inventory->stock_total;

        if ($lost > 0) {
            $newTotal = max(0, $inventory->stock_total - $lost);
        }

        $inventory->update([
            'stock_available' => $newAvailable,
            'stock_borrowed'  => $newBorrowed,
            'stock_total'     => $newTotal,
            'damaged_count'   => $inventory->damaged_count + $damaged + $poor,
            'lost_count'      => $inventory->lost_count + $lost,
        ]);

        // If many items are poor/damaged, maybe change overall condition
        if ($poor > 0 && $inventory->condition === 'good') {
            $inventory->update(['condition' => 'fair']);
        }

        $this->aiService->analyzeSyncInventory($inventory->fresh());
    }

    public function recordHistory(
        Inventory $inventory,
        int $userId,
        string $action,
        int $quantityBefore,
        int $quantityChange,
        string $notes = null,
        string $referenceType = null,
        ?int $referenceId = null
    ): void {
        InventoryHistory::create([
            'inventory_id'    => $inventory->id,
            'user_id'         => $userId,
            'action'          => $action,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after'  => $quantityBefore + $quantityChange,
            'notes'           => $notes,
            'reference_type'  => $referenceType,
            'reference_id'    => $referenceId,
        ]);
    }

    public function getLowStockItems(): \Illuminate\Database\Eloquent\Collection
    {
        return Inventory::active()->lowStock()->with('category')->get();
    }

    private function generateCode(string $name): string
    {
        $prefix = strtoupper(substr(Str::slug($name), 0, 3));
        $suffix = strtoupper(Str::random(5));
        return "INV-{$prefix}-{$suffix}";
    }
}
