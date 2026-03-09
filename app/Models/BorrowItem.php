<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowItem extends Model
{
    protected $fillable = [
        'borrow_transaction_id', 'inventory_id', 'quantity', 'quantity_returned'
    ];

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function returnTransactions()
    {
        return $this->hasMany(ReturnTransaction::class, 'borrow_item_id');
    }
}
