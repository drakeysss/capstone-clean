<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'menu_id',
        'rating',
        'comment',
        'suggestions',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the user that owns the feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu item for this feedback.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
