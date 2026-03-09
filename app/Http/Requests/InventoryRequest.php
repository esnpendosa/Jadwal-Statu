<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->inventory?->id;

        return [
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'code'           => "nullable|string|max:100|unique:inventories,code,{$id}",
            'description'    => 'nullable|string|max:2000',
            'brand'          => 'nullable|string|max:100',
            'serial_number'  => 'nullable|string|max:100',
            'unit'           => 'required|string|max:50',
            'stock_total'    => 'required|integer|min:0',
            'stock_minimum'  => 'required|integer|min:0',
            'condition'      => 'required|in:good,fair,poor,damaged',

            'purchase_date'  => 'nullable|date',
            'location'       => 'nullable|string|max:255',
            'image'          => 'nullable|image|max:2048',
        ];
    }
}
