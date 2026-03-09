<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskRule extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'points', 'category', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
