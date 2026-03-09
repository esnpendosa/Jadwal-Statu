<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'borrow_item_id'   => 'required|exists:borrow_items,id',
            'quantity_good'    => 'required|integer|min:0',
            'quantity_poor'    => 'required|integer|min:0',
            'quantity_damaged' => 'required|integer|min:0',
            'quantity_lost'    => 'required|integer|min:0',
            'condition_notes'  => 'nullable|string|max:1000',
            'notes'            => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $total = (int)$this->quantity_good + (int)$this->quantity_poor + (int)$this->quantity_damaged + (int)$this->quantity_lost;
            if ($total === 0) {
                $v->errors()->add('quantity_good', __('return.must_return_at_least_one'));
                return;
            }

            $item = \App\Models\BorrowItem::find($this->borrow_item_id);
            if ($item) {
                $maxPossible = $item->quantity - $item->quantity_returned;
                if ($total > $maxPossible) {
                    $v->errors()->add('quantity_good', "Total returned ({$total}) exceeds remaining quantity ({$maxPossible}).");
                }
            }
        });
    }
}
