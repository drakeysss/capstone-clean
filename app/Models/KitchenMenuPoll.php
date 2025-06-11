<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenMenuPoll extends Model
{
    protected $fillable = [
        'meal_name',
        'ingredients',
        'poll_date',
        'meal_type',
        'deadline',
        'status',
        'created_by',
        'sent_at'
    ];

    protected $casts = [
        'poll_date' => 'date',
        'deadline' => 'datetime',
        'sent_at' => 'datetime'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(KitchenPollResponse::class, 'poll_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the poll is active
     */
    public function isActive()
    {
        return $this->status === 'active' || $this->status === 'sent';
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('poll_date', $date);
    }

    public function scopeForMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    // Helper methods
    public function getTotalResponsesAttribute()
    {
        return $this->responses()->count();
    }

    public function getYesCountAttribute()
    {
        return $this->responses()->where('will_eat', true)->count();
    }

    public function getNoCountAttribute()
    {
        return $this->responses()->where('will_eat', false)->count();
    }

    public function getResponseRateAttribute()
    {
        $totalStudents = User::where('role', 'student')->count();
        return $totalStudents > 0 ? ($this->total_responses / $totalStudents) * 100 : 0;
    }

    public function getParticipationRateAttribute()
    {
        $totalStudents = User::where('role', 'student')->count();
        return $totalStudents > 0 ? ($this->yes_count / $totalStudents) * 100 : 0;
    }

    public function canBeEdited()
    {
        // Allow editing for both draft and active polls
        // Kitchen staff should be able to update deadlines for active polls
        return in_array($this->status, ['draft', 'active']);
    }

    public function canBeSent()
    {
        return $this->status === 'draft';
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'active',
            'sent_at' => now()
        ]);
    }

    public function close()
    {
        $this->update(['status' => 'closed']);
    }
}
