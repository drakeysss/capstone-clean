<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meal_type',
        'menu_item',
        'calories',
        'protein',
        'carbs',
        'fats',
        'consumed_at',
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
        'consumed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }

    public function meal()
    {
        return $this->belongsTo('App\Models\Meal');
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where('meal_name', 'like', '%' . request('search') . '%')
                ->orWhere('meal_id', 'like', '%' . request('search') . '%');
        }
    }

    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('consumption_date', $date);
    }

    public function scopeFilterByMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }
}
