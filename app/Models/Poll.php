<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'poll_date',
        'meal_type',
        'instructions',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'poll_date' => 'date',
    ];

    /**
     * Get the user who created the poll.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the menu items associated with this poll.
     */
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'poll_menu_items')
            ->withTimestamps();
    }

    /**
     * Get the responses for this poll.
     */
    public function responses()
    {
        return $this->hasMany(PollResponse::class);
    }
}
