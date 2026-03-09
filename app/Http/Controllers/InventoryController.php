<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryRequest;
use App\Models\Category;
use App\Models\Inventory;
use App\Services\InventoryService;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $service) {}

    public function index(Request $request)
    {
        $query = Inventory::with('category')
            ->active()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
            ->when($request->stock_status === 'low', fn($q) => $q->lowStock())
            ->when($request->stock_status === 'available', fn($q) => $q->available())
            ->latest();

        $inventories = $query->paginate(15)->withQueryString();
        $categories  = Category::active()->get();
        $lowStockCount = Inventory::active()->lowStock()->count();

        return view('inventory.index', compact('inventories', 'categories', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('inventory.create', compact('categories'));
    }

    public function store(InventoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $inventory = $this->service->create($data);
        AuditLog::log('create', "Created inventory: {$inventory->name}", $inventory);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', __('inventory.created'));
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['category', 'creator', 'histories.user']);
        $activeBorrows = $inventory->borrows()
            ->whereHas('borrowTransaction', fn($q) => $q->whereIn('status', ['approved', 'borrowed']))
            ->with(['borrowTransaction.project', 'borrowTransaction.requester'])
            ->latest()
            ->get();

        return view('inventory.show', compact('inventory', 'activeBorrows'));
    }

    public function edit(Inventory $inventory)
    {
        $categories = Category::active()->get();
        return view('inventory.edit', compact('inventory', 'categories'));
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        $data = $request->validated();

        // Handle image removal
        if ($request->boolean('remove_image') && $inventory->image) {
            \Storage::disk('public')->delete($inventory->image);
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($inventory->image) {
                \Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $old = $inventory->toArray();
        $this->service->update($inventory, $data);
        AuditLog::log('update', "Updated inventory: {$inventory->name}", $inventory, $old, $data);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', __('inventory.updated'));
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->stock_borrowed > 0) {
            return back()->with('error', __('inventory.cannot_delete_borrowed'));
        }

        AuditLog::log('delete', "Deleted inventory: {$inventory->name}", $inventory);
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', __('inventory.deleted'));
    }

    public function history(Inventory $inventory)
    {
        $histories = $inventory->histories()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('inventory.history', compact('inventory', 'histories'));
    }

    public function adjustStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'adjustment' => 'required|integer|not_in:0',
            'reason'     => 'required|string|max:255',
        ]);

        $adjustment = (int) $request->adjustment;
        $newTotal   = $inventory->stock_total + $adjustment;
        $newAvailable = $inventory->stock_available + $adjustment;

        if ($newTotal < 0 || $newAvailable < 0) {
            return back()->with('error', __('inventory.invalid_adjustment'));
        }

        $old = ['stock_total' => $inventory->stock_total, 'stock_available' => $inventory->stock_available];
        $inventory->update([
            'stock_total'     => $newTotal,
            'stock_available' => max(0, $newAvailable),
        ]);

        $this->service->recordHistory($inventory, auth()->id(), 'adjusted',
            $old['stock_available'], $adjustment, $request->reason);

        return back()->with('success', __('inventory.stock_adjusted'));
    }
}
