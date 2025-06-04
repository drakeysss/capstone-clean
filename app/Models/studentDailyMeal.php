<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class studentDailyMeal extends Model
{
    protected $fillable = ['meal_id', 'meal_name', 'consumption_date', 'meal_type'];

    use HasFactory;
    

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }
    public function meal()
    {
        return $this->belongsTo('App\Models\Meal');
    }

}
