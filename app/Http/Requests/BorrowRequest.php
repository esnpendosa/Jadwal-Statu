<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'           => 'required|exists:projects,id',
            'items'                => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'expected_return_date' => 'required|date|after_or_equal:today',
            'purpose'              => 'nullable|string|max:1000',
            'notes'                => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->has('items')) {
                foreach ($this->items as $index => $itemData) {
                    $inventory = \App\Models\Inventory::find($itemData['inventory_id'] ?? null);
                    if ($inventory && $inventory->stock_available < ($itemData['quantity'] ?? 0)) {
                        $v->errors()->add("items.{$index}.quantity",
                            __('borrow.insufficient_stock', [
                                'available' => "{$inventory->stock_available} ({$inventory->name})"
                            ])
                        );
                    }
                }
            }
        });
    }
}
