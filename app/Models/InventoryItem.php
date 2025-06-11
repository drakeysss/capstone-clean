<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'quantity',
        'unit',
        'minimum_quantity',
        'reorder_point',
        'unit_price',
        'supplier',
        'location',
        'expiry_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'expiry_date' => 'date'
    ];

    public function history()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function scopeLowStock($query)
    {
        return $query->where('quantity', '<=', 'reorder_point');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', now()->addDays($days));
    }
} 