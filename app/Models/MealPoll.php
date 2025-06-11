<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPoll extends Model
{
    protected $fillable = [
        'meal_id',
        'poll_date',
        'meal_type',
        'votes',
        'is_active'
    ];

    protected $casts = [
        'poll_date' => 'date',
        'is_active' => 'boolean',
        'votes' => 'integer'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function responses()
    {
        return $this->hasMany(MealPollResponse::class, 'poll_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('poll_date', $date);
    }

    public function scopeForMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    public function getAttendanceCount()
    {
        return $this->responses()->where('will_attend', true)->count();
    }
} 