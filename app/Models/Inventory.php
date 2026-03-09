<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'category_id', 'description', 'brand', 'serial_number',
        'unit', 'stock_total', 'stock_available', 'stock_borrowed', 'stock_minimum',
        'condition', 'purchase_date', 'location', 'image',
        'damaged_count', 'lost_count', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'purchase_date'   => 'date',
    ];

    // ==================
    // Relationships
    // ==================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function borrowItems()
    {
        return $this->hasMany(BorrowItem::class);
    }

    // Alias for view compatibility
    public function borrows()
    {
        return $this->borrowItems();
    }

    // ==================
    // Computed
    // ==================

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/default-item.png');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_available <= $this->stock_minimum;
    }

    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            'good'    => 'badge-success',
            'fair'    => 'badge-warning',
            'poor'    => 'badge-danger',
            'damaged' => 'badge-danger',
            default   => 'badge-gray',
        };
    }

    // ==================
    // Scopes
    // ==================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_available <= stock_minimum');
    }

    public function scopeAvailable($query)
    {
        return $query->where('stock_available', '>', 0)->where('is_active', true);
    }

    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
