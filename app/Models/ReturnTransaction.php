<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    protected $fillable = [
        'code', 'borrow_transaction_id', 'borrow_item_id', 'returned_by', 'received_by',
        'quantity_returned', 'quantity_good', 'quantity_poor', 'quantity_damaged',
        'quantity_lost', 'condition_notes', 'is_late', 'days_late',
        'status', 'notes',
    ];

    protected $casts = [
        'is_late' => 'boolean',
    ];

    // ==================
    // Relationships
    // ==================

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function borrowItem()
    {
        return $this->belongsTo(BorrowItem::class);
    }

    // Alias for view compatibility
    public function borrow()
    {
        return $this->borrowTransaction();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    // ==================
    // Computed
    // ==================

    public function getConditionStatusAttribute(): string
    {
        if ($this->quantity_lost > 0) return 'lost';
        if ($this->quantity_damaged > 0) return 'damaged';
        if ($this->quantity_poor > 0) return 'poor';
        return 'good';
    }

    public function getHasMismatchAttribute(): bool
    {
        return $this->quantity_lost > 0 || 
               ($this->quantity_returned < ($this->borrowItem?->quantity ?? 0));
    }

    public function getHasDamageAttribute(): bool
    {
        return $this->quantity_damaged > 0;
    }
}
