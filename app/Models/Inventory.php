<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'quantity',
        'unit',
        'category',
        'expiry_date',
        'minimum_stock'
    ];

    protected $casts = [
        'expiry_date' => 'date'
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'inventory_item_id');
    }
}
