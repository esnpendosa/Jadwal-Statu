<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description', 'location', 'client_name',
        'start_date', 'end_date', 'status', 'pic_id', 'manager_name',
        'created_by', 'budget', 'risk_score', 'risk_level',
        'google_calendar_event_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'budget'     => 'decimal:2',
    ];

    // ==================
    // Relationships
    // ==================

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * manager() tetap ada sebagai virtual accessor agar view tidak error.
     * Mengembalikan object sederhana dengan property 'name'.
     */
    public function getManagerAttribute()
    {
        if (!$this->manager_name) return null;
        return (object) ['name' => $this->manager_name, 'email' => ''];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function borrowTransactions()
    {
        return $this->hasMany(BorrowTransaction::class);
    }

    // Alias for view compatibility
    public function borrows()
    {
        return $this->borrowTransactions();
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function aiSuggestions()
    {
        return $this->morphMany(AiSuggestion::class, 'target', 'target_type', 'target_id')
                    ->where('target_type', 'project');
    }

    // ==================
    // Computed
    // ==================

    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date < now() && $this->status === 'active';
    }

    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->risk_level) {
            'critical' => 'badge-danger',
            'high'     => 'badge-danger',
            'medium'   => 'badge-warning',
            'low'      => 'badge-success',
            default    => 'badge-gray',
        };
    }

    public function getRiskScoreColorAttribute(): string
    {
        if ($this->risk_score >= 80) return 'text-red-500';
        if ($this->risk_score >= 50) return 'text-amber-500';
        return 'text-emerald-500';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'badge-success',
            'draft'     => 'badge-gray',
            'completed' => 'badge-primary',
            'cancelled' => 'badge-danger',
            default     => 'badge-gray',
        };
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getRemainingDaysAttribute(): int
    {
        if ($this->status !== 'active') return 0;
        return max(0, now()->diffInDays($this->end_date, false));
    }

    // ==================
    // Scopes
    // ==================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())->where('status', 'active');
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeByPic($query, $userId)
    {
        return $query->where('pic_id', $userId);
    }
}
