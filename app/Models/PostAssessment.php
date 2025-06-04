<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'meal_type',
        'menu_id',
        'user_id',
        'prepared_quantity',
        'leftover_quantity',
        'wastage_percentage',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'prepared_quantity' => 'decimal:2',
        'leftover_quantity' => 'decimal:2',
        'wastage_percentage' => 'decimal:2',
    ];

    /**
     * Get the user that created the assessment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu item for this assessment.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
