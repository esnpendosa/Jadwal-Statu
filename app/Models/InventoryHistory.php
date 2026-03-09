<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $fillable = [
        'inventory_id', 'user_id', 'action', 'quantity_before',
        'quantity_change', 'quantity_after', 'notes', 'reference_type', 'reference_id',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'added'    => 'badge-success',
            'adjusted' => 'badge-warning',
            'borrowed' => 'badge-primary',
            'returned' => 'badge-success',
            'damaged'  => 'badge-danger',
            'lost'     => 'badge-danger',
            default    => 'badge-gray',
        };
    }
}
