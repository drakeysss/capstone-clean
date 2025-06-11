<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'unit',
        'category',
        'reorder_point',
        'supplier',
        'location',
        'unit_price',
        'last_updated_by',
        'status'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'unit_price' => 'decimal:2'
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'inventory_item_id');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function checkItems()
    {
        return $this->hasMany(InventoryCheckItem::class, 'ingredient_id');
    }

    public function history()
    {
        return $this->hasMany(InventoryHistory::class, 'inventory_item_id');
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        // Since expiry_date column doesn't exist, return empty query
        return $query->whereRaw('1 = 0'); // Always false
    }

    // Helper methods
    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_point;
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    public function isExpiringSoon($days = 7)
    {
        // Since expiry_date column doesn't exist, always return false
        return false;
    }

    public function getStatusAttribute($value)
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } elseif ($this->isExpiringSoon()) {
            return 'expired';
        }
        return 'available';
    }
}
