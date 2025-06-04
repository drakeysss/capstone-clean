<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'metric_type',
        'date',
        'value',
        'unit'
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 