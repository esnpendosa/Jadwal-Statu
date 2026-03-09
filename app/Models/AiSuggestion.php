<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    protected $fillable = [
        'ai_suggestion_rule_id', 'target_type', 'target_id', 'target_label',
        'suggestion_text', 'severity', 'is_read', 'is_dismissed', 'generated_at',
    ];

    protected $casts = [
        'suggestion_text' => 'array',
        'is_read'         => 'boolean',
        'is_dismissed'    => 'boolean',
        'generated_at'    => 'datetime',
    ];

    public function rule()
    {
        return $this->belongsTo(AiSuggestionRule::class, 'ai_suggestion_rule_id');
    }

    public function getSuggestionForLocale(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $text = $this->suggestion_text;
        return $text[$locale] ?? $text['id'] ?? $text['en'] ?? '';
    }

    public function getSeverityBadgeAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'badge-danger',
            'warning'  => 'badge-warning',
            'info'     => 'badge-primary',
            default    => 'badge-gray',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false)->where('is_dismissed', false);
    }
}
