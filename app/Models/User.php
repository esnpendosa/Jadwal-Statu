<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'preferred_language',
        'timezone',
        'is_active',
        'google_calendar_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_calendar_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'is_active'            => 'boolean',
            'google_calendar_token'=> 'encrypted',
        ];
    }

    // ==================
    // Relationships
    // ==================

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function projectsAsPic()
    {
        return $this->hasMany(Project::class, 'pic_id');
    }

    // manager_name kini berupa string bebas, tidak ada relasi FK ke users

    public function borrowTransactions()
    {
        return $this->hasMany(BorrowTransaction::class, 'requested_by');
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function aiSuggestions()
    {
        return $this->morphMany(AiSuggestion::class, 'target', 'target_type', 'target_id')
                    ->where('target_type', 'user');
    }

    // ==================
    // Computed / Helpers
    // ==================

    public function getTotalRiskScoreAttribute(): int
    {
        return (int) $this->riskScores()->sum('points_added');
    }

    public function getRiskScoreAttribute(): int
    {
        return $this->total_risk_score;
    }

    public function getRiskLevelAttribute(): string
    {
        $score = $this->total_risk_score;
        if ($score >= 20) return 'critical';
        if ($score >= 10) return 'high';
        if ($score >= 5)  return 'medium';
        return 'low';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $initial = strtoupper(substr($this->name, 0, 2));
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff&size=80&bold=true';
    }

    public function getLocaleAttribute(): string
    {
        return $this->preferred_language ?? config('app.locale', 'id');
    }

    // ==================
    // Scopes
    // ==================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
