<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class studentSetting extends Model
{
    protected $fillable = ['user_id', 'setting_name', 'setting_value'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where('setting_name', 'like', '%' . request('search') . '%')
                ->orWhere('setting_value', 'like', '%' . request('search') . '%');
        }
    }
    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }
    
}
