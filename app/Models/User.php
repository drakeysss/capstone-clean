<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the dashboard route based on user role.
     *
     * @return string
     */
    public function getDashboardRoute()
    {
        return match($this->role) {
            'admin' => 'cook.dashboard',
            'student' => 'student.dashboard',
            'cook' => 'cook.dashboard',
            'kitchen' => 'kitchen.dashboard',
            default => 'cook.dashboard',
        };
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can manage other users
     *
     * @return bool
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage system settings
     *
     * @return bool
     */
    public function canManageSettings(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can view all data
     *
     * @return bool
     */
    public function canViewAllData(): bool
    {
        return $this->isAdmin();
    }

    public function studentMeals()
    {
        return $this->hasMany(StudentMeal::class);
    }

    public function studentReports()
    {
        return $this->hasMany(StudentReport::class);
    }

    public function studentAnalytics()
    {
        return $this->hasMany(StudentAnalytic::class);
    }

    public function cookTasks()
    {
        return $this->hasMany(CookDashboard::class);
    }
    
    /**
     * Set the user's password - ensures it's always hashed
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
        }
    }
}
