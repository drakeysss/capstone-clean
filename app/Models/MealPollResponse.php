<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPollResponse extends Model
{
    protected $fillable = [
        'poll_id',
        'student_id',
        'will_attend',
        'preference_notes'
    ];

    protected $casts = [
        'will_attend' => 'boolean'
    ];

    public function poll()
    {
        return $this->belongsTo(MealPoll::class, 'poll_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopeAttending($query)
    {
        return $query->where('will_attend', true);
    }

    public function scopeNotAttending($query)
    {
        return $query->where('will_attend', false);
    }
} 