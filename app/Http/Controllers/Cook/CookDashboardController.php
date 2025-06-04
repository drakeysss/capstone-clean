<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Dashboard\BaseDashboardController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Inventory;
use App\Models\Supplier;

class CookDashboardController extends BaseDashboardController
{
    public function __construct()
    {
        parent::__construct('cook', 'cook');
    }

    protected function getDashboardData()
    {
        // Get order statistics
        $pendingOrders = Order::whereIn('status', ['pending', 'preparing'])->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Get menu statistics
        $activeMenuItems = Menu::where('is_available', true)->count();
        $totalMenuItems = Menu::count();
        $menuItems = Menu::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get inventory statistics
        $lowStockItems = Inventory::where('quantity', '<=', \DB::raw('reorder_point'))->count();
        $totalItems = Inventory::count();
        $lowStockItemsList = Inventory::where('quantity', '<=', \DB::raw('reorder_point'))
            ->orderBy('quantity')
            ->take(3)
            ->get();

        // Get supplier statistics
        $activeSuppliers = Supplier::count();
        $recentSuppliers = Supplier::orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get recent orders
        $recentOrders = Order::with(['items.menu'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get meal attendance data for food waste prevention
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $weekEnd = now()->endOfWeek()->format('Y-m-d');
        $lastWeekStart = now()->subWeek()->startOfWeek()->format('Y-m-d');
        $lastWeekEnd = now()->subWeek()->endOfWeek()->format('Y-m-d');
        
        // Get today's meal attendance
        $todayAttendance = \App\Models\PreOrder::where('date', $today)
            ->where('is_attending', true)
            ->count();
            
        // Get this week's meal attendance
        $thisWeekAttendance = \App\Models\PreOrder::whereBetween('date', [$weekStart, $weekEnd])
            ->where('is_attending', true)
            ->count();
            
        // Get last week's meal attendance
        $lastWeekAttendance = \App\Models\PreOrder::whereBetween('date', [$lastWeekStart, $lastWeekEnd])
            ->where('is_attending', true)
            ->count();
            
        // Calculate percentage increase in attendance responses
        $attendanceIncrease = $lastWeekAttendance > 0 ? 
            round((($thisWeekAttendance - $lastWeekAttendance) / $lastWeekAttendance) * 100) : 0;
            
        // Get meal poll responses
        $mealPolls = \App\Models\Announcement::where('is_poll', true)
            ->where('is_active', true)
            ->get();
            
        $totalPollResponses = 0;
        foreach ($mealPolls as $poll) {
            $totalPollResponses += $poll->pollResponses()->count();
        }
        
        // Calculate waste reduction metrics
        // Assuming each accurate attendance record saves approximately 0.5kg of food waste
        $kgSaved = $thisWeekAttendance * 0.5;
        
        // Assuming each kg of food costs approximately $5
        $costSaved = $kgSaved * 5;
        
        // Assuming each meal is approximately 0.3kg
        $mealsSaved = round($kgSaved / 0.3);
        
        // Get tomorrow's meal with lowest attendance percentage for recommendation
        $tomorrowMeals = \App\Models\Menu::where('date', $tomorrow)->get();
        $lowestAttendanceMeal = 'dinner';
        $lowestAttendancePercentage = 100;
        
        foreach ($tomorrowMeals as $meal) {
            $totalStudents = \App\Models\User::where('role', 'student')->count();
            $attendingStudents = \App\Models\PreOrder::where('date', $tomorrow)
                ->where('meal_type', $meal->meal_type)
                ->where('is_attending', true)
                ->count();
                
            $attendancePercentage = $totalStudents > 0 ? ($attendingStudents / $totalStudents) * 100 : 0;
            
            if ($attendancePercentage < $lowestAttendancePercentage) {
                $lowestAttendancePercentage = $attendancePercentage;
                $lowestAttendanceMeal = $meal->meal_type;
            }
        }
        
        // Calculate recommended reduction percentage
        $recommendedReduction = round(100 - $lowestAttendancePercentage);
        if ($recommendedReduction < 10) $recommendedReduction = 10;
        if ($recommendedReduction > 40) $recommendedReduction = 40;

        // Prepare meal attendance data for the view
        $mealAttendance = [
            'today' => $todayAttendance,
            'total' => $thisWeekAttendance,
            'increase' => $attendanceIncrease,
            'poll_responses' => $totalPollResponses
        ];
        
        // Prepare waste reduction data for the view
        $wasteReduction = [
            'percentage' => round(($thisWeekAttendance / max(1, $thisWeekAttendance + $lastWeekAttendance)) * 100),
            'kg_saved' => round($kgSaved),
            'cost_saved' => round($costSaved),
            'meals_saved' => $mealsSaved,
            'recommendation' => $recommendedReduction . '%',
            'meal_type' => ucfirst($lowestAttendanceMeal)
        ];
        
        return compact(
            'pendingOrders',
            'completedOrders',
            'activeMenuItems',
            'totalMenuItems',
            'menuItems',
            'lowStockItems',
            'totalItems',
            'lowStockItemsList',
            'activeSuppliers',
            'recentSuppliers',
            'recentOrders',
            'mealAttendance',
            'wasteReduction'
        );
    }

    public function consumption()
    {
        return view('cook.consumption');
    }

    public function orders()
    {
        return view('cook.orders');
    }

    public function menu()
    {
        $menuItems = \App\Models\Menu::with('ingredients')->get();
        return view('cook.menu', compact('menuItems'));
    }

    public function inventory()
    {
        return view('cook.inventory');
    }

    public function profile()
    {
        return view('cook.profile');
    }

    public function suppliers()
    {
        return view('cook.suppliers');
    }

    public function settings()
    {
        return view('cook.settings');
    }
    
    /**
     * Display the meal attendance and food waste prevention dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function mealAttendance()
    {
        // Get date ranges for data collection
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $weekEnd = now()->endOfWeek()->format('Y-m-d');
        $lastWeekStart = now()->subWeek()->startOfWeek()->format('Y-m-d');
        $lastWeekEnd = now()->subWeek()->endOfWeek()->format('Y-m-d');
        
        // Get meal attendance data for each meal type
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        $mealAttendance = [];
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        
        // Get attendance for each meal type
        foreach ($mealTypes as $mealType) {
            // Today's attendance
            $attending = \App\Models\PreOrder::where('date', $today)
                ->where('meal_type', $mealType)
                ->where('is_attending', true)
                ->count();
                
            $notAttending = \App\Models\PreOrder::where('date', $today)
                ->where('meal_type', $mealType)
                ->where('is_attending', false)
                ->count();
                
            $undecided = $totalStudents - ($attending + $notAttending);
            
            $mealAttendance[$mealType] = $attending;
            $mealAttendance[$mealType . '_not'] = $notAttending;
            $mealAttendance[$mealType . '_undecided'] = $undecided;
            $mealAttendance[$mealType . '_percentage'] = $totalStudents > 0 ? round(($attending / $totalStudents) * 100) : 0;
        }
        
        // Get this week's attendance data
        $thisWeekAttendance = \App\Models\PreOrder::whereBetween('date', [$weekStart, $weekEnd])
            ->where('is_attending', true)
            ->count();
            
        // Get last week's attendance data
        $lastWeekAttendance = \App\Models\PreOrder::whereBetween('date', [$lastWeekStart, $lastWeekEnd])
            ->where('is_attending', true)
            ->count();
            
        // Calculate percentage increase in attendance responses
        $attendanceIncrease = $lastWeekAttendance > 0 ? 
            round((($thisWeekAttendance - $lastWeekAttendance) / $lastWeekAttendance) * 100) : 0;
        
        // Get meal poll responses
        $mealPolls = \App\Models\Announcement::where('is_poll', true)
            ->where('is_active', true)
            ->get();
            
        $pollResponses = [];
        foreach ($mealPolls as $poll) {
            $responses = $poll->pollResponses;
            $pollResponses[$poll->id] = [
                'total' => $responses->count(),
                'will_attend' => $responses->where('response', 'Will Attend')->count(),
                'will_not_attend' => $responses->where('response', 'Will Not Attend')->count(),
                'undecided' => $responses->where('response', 'Undecided')->count(),
            ];
        }
        
        // Calculate waste reduction metrics
        // Assuming each accurate attendance record saves approximately 0.5kg of food waste
        $kgSaved = $thisWeekAttendance * 0.5;
        
        // Assuming each kg of food costs approximately $5
        $costSaved = $kgSaved * 5;
        
        // Assuming each meal is approximately 0.3kg
        $mealsSaved = round($kgSaved / 0.3);
        
        // Get historical waste data (placeholder data for demonstration)
        $historicalData = [
            'weeks' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current Week'],
            'waste_kg' => [120, 105, 95, 80, 65],
            'attendance_percentage' => [65, 70, 75, 82, 88],
        ];
        
        // Prepare data for the view
        $mealAttendance['total'] = $thisWeekAttendance;
        $mealAttendance['increase'] = $attendanceIncrease;
        
        $wasteReduction = [
            'percentage' => round(($thisWeekAttendance / max(1, $thisWeekAttendance + $lastWeekAttendance)) * 100),
            'kg_saved' => round($kgSaved),
            'cost_saved' => round($costSaved),
            'meals_saved' => $mealsSaved,
        ];
        
        return view('cook.meal-attendance', compact(
            'mealAttendance',
            'wasteReduction',
            'pollResponses',
            'mealPolls',
            'historicalData'
        ));
    }

    public function reports()
    {
        return view('cook.reports');
    }

    public function notifications()
    {
        return view('cook.notifications');
    }
}
