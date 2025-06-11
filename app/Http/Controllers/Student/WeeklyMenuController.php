<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklyMenu;
use Carbon\Carbon;

class WeeklyMenuController extends Controller
{
    /**
     * Display the weekly menu for students
     */
    public function index()
    {
        // FIXED: Use consistent week of month calculation
        // Use Laravel's built-in weekOfMonth method for consistency
        $weekOfMonth = Carbon::now()->weekOfMonth;

        // For weeks 1 & 3, use week_cycle = 1
        // For weeks 2 & 4, use week_cycle = 2
        $weekCycle = ($weekOfMonth % 2 === 1) ? 1 : 2;
        
        // Get menus for the current week cycle
        $weeklyMenus = $this->getMenusByWeekCycle($weekCycle);
        
        // Get today's menu
        $today = strtolower(Carbon::now()->format('l'));
        $todayMenu = $weeklyMenus[$today] ?? null;
        
        return view('student.weekly-menu', compact('weeklyMenus', 'todayMenu', 'weekCycle'));
    }
    
    /**
     * Helper method to get menus by week cycle
     */
    private function getMenusByWeekCycle($weekCycle)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        
        $menus = [];
        
        foreach ($days as $day) {
            $menus[$day] = [];
            
            foreach ($mealTypes as $mealType) {
                $menu = WeeklyMenu::where('week_cycle', $weekCycle)
                    ->where('day_of_week', $day)
                    ->where('meal_type', $mealType)
                    ->where('is_available', true)
                    ->first();
                
                $menus[$day][$mealType] = $menu ?? null;
            }
        }
        
        return $menus;
    }
}
