<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSuggestionRule extends Model
{
    protected $fillable = [
        'trigger_type', 'threshold', 'period_days', 'target',
        'suggestion', 'severity', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'suggestion' => 'array',
    ];

    public function aiSuggestions()
    {
        return $this->hasMany(AiSuggestion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
