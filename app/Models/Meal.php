<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'name',
        'ingredients',
        'prep_time',
        'cooking_time',
        'serving_size',
        'meal_type',
        'day_of_week',
        'week_cycle'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'prep_time' => 'integer',
        'cooking_time' => 'integer',
        'serving_size' => 'integer',
        'week_cycle' => 'integer'
    ];

    public function polls()
    {
        return $this->hasMany(KitchenMenuPoll::class, 'meal_id');
    }

    public function scopeForWeekCycle($query, $weekCycle)
    {
        return $query->where('week_cycle', $weekCycle);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    public function scopeForMealType($query, $mealType)
    {
        return $query->where('meal_type', strtolower($mealType));
    }

    public function getCurrentPoll($date)
    {
        return $this->polls()
            ->where('poll_date', $date)
            ->where('status', 'active')
            ->first();
    }
} 