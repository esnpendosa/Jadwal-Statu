<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'code', 'subject', 'body', 'is_active',
    ];

    protected $casts = [
        'subject'   => 'array',
        'body'      => 'array',
        'is_active' => 'boolean',
    ];

    public function getSubjectForLocale(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->subject[$locale] ?? $this->subject['id'] ?? $this->subject['en'] ?? '';
    }

    public function getBodyForLocale(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->body[$locale] ?? $this->body['id'] ?? $this->body['en'] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
