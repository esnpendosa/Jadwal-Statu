<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowTransaction extends Model
{
    protected $fillable = [
        'code', 'project_id', 'requested_by', 'approved_by',
        'status', 'borrow_date', 'expected_return_date',
        'actual_return_date', 'purpose', 'notes', 'google_calendar_event_id',
    ];

    protected $casts = [
        'borrow_date'          => 'date',
        'expected_return_date' => 'date',
        'actual_return_date'   => 'date',
    ];

    // ==================
    // Relationships
    // ==================

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(BorrowItem::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Alias for view compatibility
    public function user()
    {
        return $this->requester();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnTransactions()
    {
        return $this->hasMany(ReturnTransaction::class, 'borrow_transaction_id');
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    // ==================
    // Computed
    // ==================

    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_return_date < now()->toDateString()
            && !in_array($this->status, ['completed', 'rejected']);
    }

    public function getDaysLateAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return now()->diffInDays($this->expected_return_date);
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->status_badge_class;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'badge-warning',
            'approved'  => 'badge-primary',
            'borrowed'  => 'badge-primary',
            'completed' => 'badge-success',
            'rejected'  => 'badge-danger',
            default     => 'badge-gray',
        };
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    public function getTotalReturnedAttribute(): int
    {
        return $this->items()->sum('quantity_returned');
    }

    public function getQuantityRemainingAttribute(): int
    {
        return max(0, $this->total_quantity - $this->total_returned);
    }

    // ==================
    // Scopes
    // ==================

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'borrowed']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_return_date', '<', now()->toDateString())
                     ->whereNotIn('status', ['completed', 'rejected']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
