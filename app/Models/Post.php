<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_path',
        'caption',
        'content_type',
        'platforms',
        'schedule_time',
        'status',
        'error_message',
    ];

    protected $casts = [
        'platforms' => 'array',
        'schedule_time' => 'datetime',
    ];

    /**
     * Get full absolute URL for the media.
     * Uses the 'public' disk and prepends APP_URL.
     */
    public function getMediaUrlAttribute(): ?string
    {
        if (!$this->media_path) {
            return null;
        }

        // Storage::disk('public')->url() returns /storage/xxx
        // We need a full URL like http://localhost:8000/storage/xxx
        return rtrim(config('app.url'), '/') . Storage::disk('public')->url($this->media_path);
    }

    /**
     * Get the local filesystem path to the media file.
     */
    public function getMediaLocalPathAttribute(): ?string
    {
        if (!$this->media_path) {
            return null;
        }

        return Storage::disk('public')->path($this->media_path);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduledBefore($query, $datetime)
    {
        return $query->where('schedule_time', '<=', $datetime);
    }
}
