<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Dashboard\BaseDashboardController;
use Illuminate\Http\Request;

class KitchenDashboardController extends BaseDashboardController
{
    public function __construct()
    {
        parent::__construct('kitchen', 'kitchen');
    }

    protected function getDashboardData()
    {
        $data = parent::getDashboardData();
        
        // Get data from Cook's menu planning to execute in kitchen
        $today = now()->format('Y-m-d');
        $currentDay = strtolower(now()->format('l'));
        $weekNumber = now()->weekOfYear;
        $weekCycle = $weekNumber % 2 === 1 ? 1 : 2; // Odd weeks are cycle 1, even weeks are cycle 2
        
        // Get today's menu items from cook's planning
        $todayMenus = \App\Models\Menu::where('day', ucfirst($currentDay))
            ->where('week_cycle', $weekCycle)
            ->get()
            ->groupBy('meal_type');
        
        // Get inventory items that need preparation
        $inventoryItems = \App\Models\Inventory::where('quantity', '<=', \DB::raw('reorder_point * 1.5'))
            ->orderBy('quantity')
            ->take(5)
            ->get();
        
        // Get pre-orders for today to prepare correct quantities
        $preOrders = \App\Models\PreOrder::where('date', $today)
            ->where('is_attending', true)
            ->get()
            ->groupBy('meal_type');
        
        // Count expected attendance for each meal type
        $mealAttendance = [
            'breakfast' => $preOrders->get('breakfast') ? $preOrders->get('breakfast')->count() : 0,
            'lunch' => $preOrders->get('lunch') ? $preOrders->get('lunch')->count() : 0,
            'dinner' => $preOrders->get('dinner') ? $preOrders->get('dinner')->count() : 0,
        ];
        
        // Add kitchen-specific data to the dashboard
        $data['todayMenus'] = $todayMenus;
        $data['inventoryItems'] = $inventoryItems;
        $data['mealAttendance'] = $mealAttendance;
        $data['recentOrders'] = $data['recentOrders']->take(5);
        
        return $data;
    }

    // Recipe & Meal - Execute recipes created by cooks
    public function recipes()
    {
        // Get recipes created by cooks to be executed by kitchen staff
        $recipes = \App\Models\Menu::where('is_available', true)
            ->orderBy('name')
            ->get()
            ->unique('name');
            
        return view('kitchen.recipes', ['recipes' => $recipes]);
    }

    public function mealPlanning()
    {
        // Get meal planning from cooks to prepare for execution
        $weekNumber = now()->weekOfYear;
        $weekCycle = $weekNumber % 2 === 1 ? 1 : 2;
        
        // Get the current week's menu created by cooks
        $menus = [
            1 => [], // Week 1 & 3
            2 => []  // Week 2 & 4
        ];
        
        // Get menus for current cycle to execute
        $cycleMenus = \App\Models\Menu::where('week_cycle', $weekCycle)->get();
        
        foreach ($cycleMenus as $menu) {
            if (isset($menu->day) && isset($menu->meal_type)) {
                if (!isset($menus[$weekCycle][$menu->day])) {
                    $menus[$weekCycle][$menu->day] = [];
                }
                $menus[$weekCycle][$menu->day][$menu->meal_type] = [
                    'name' => $menu->name,
                    'ingredients' => $menu->description,
                    'id' => $menu->id
                ];
            }
        }
        
        return view('kitchen.meal-planning', [
            'menus' => $menus,
            'currentCycle' => $weekCycle
        ]);
    }

    public function preparation()
    {
        // Get today's meals that need preparation
        $today = now()->format('Y-m-d');
        $currentDay = now()->format('l');
        $weekNumber = now()->weekOfYear;
        $weekCycle = $weekNumber % 2 === 1 ? 1 : 2;
        
        // Get today's menu items from cook's planning
        $todayMenuItems = \App\Models\Menu::where('day', $currentDay)
            ->where('week_cycle', $weekCycle)
            ->get()
            ->groupBy('meal_type');
        
        // Get pre-orders to know quantities
        $preOrders = \App\Models\PreOrder::where('date', $today)
            ->where('is_attending', true)
            ->get()
            ->groupBy('meal_type');
            
        // Count expected attendance for each meal type
        $mealAttendance = [
            'breakfast' => $preOrders->get('breakfast') ? $preOrders->get('breakfast')->count() : 0,
            'lunch' => $preOrders->get('lunch') ? $preOrders->get('lunch')->count() : 0,
            'dinner' => $preOrders->get('dinner') ? $preOrders->get('dinner')->count() : 0,
        ];
        
        return view('kitchen.preparation', [
            'todayMenuItems' => $todayMenuItems,
            'mealAttendance' => $mealAttendance,
            'currentDay' => $currentDay
        ]);
    }

    public function orders()
    {
        // Get orders that need to be prepared by kitchen staff
        $pendingOrders = \App\Models\Order::whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at')
            ->with(['items.menu'])
            ->get();
            
        // Get completed orders for reference
        $completedOrders = \App\Models\Order::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->with(['items.menu'])
            ->get();
            
        return view('kitchen.orders', [
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders
        ]);
    }

    // Inventory Management - Execute inventory tasks assigned by cook/admin
    public function inventory()
    {
        // Get inventory items that need attention
        $lowStockItems = \App\Models\Inventory::where('quantity', '<=', \DB::raw('reorder_point'))
            ->orderBy('quantity')
            ->get();
            
        // Get all inventory items for reference
        $allItems = \App\Models\Inventory::orderBy('name')->get();
        
        return view('kitchen.inventory', [
            'lowStockItems' => $lowStockItems,
            'allItems' => $allItems
        ]);
    }

    public function suppliers()
    {
        return view('kitchen.suppliers');
    }

    public function purchases()
    {
        return view('kitchen.purchases');
    }

    // Reports & Analytics
    public function reports()
    {
        return view('kitchen.reports');
    }

    public function analytics()
    {
        $data = $this->getAnalyticsData();
        return view('kitchen.analytics', $data);
    }

    // Alerts & Notifications
    public function notifications()
    {
        return view('kitchen.notifications');
    }

    public function alerts()
    {
        return view('kitchen.alerts');
    }

    // Settings
    public function settings()
    {
        return view('kitchen.settings');
    }
    
    /**
     * Display the daily menu for the kitchen staff based on cook's planning.
     * Kitchen staff uses this to execute the menu created by cooks.
     *
     * @return \Illuminate\Http\Response
     */
    public function dailyMenu()
    {
        // Get today's date and current week cycle
        $today = now()->format('Y-m-d');
        $currentDay = now()->format('l');
        $weekNumber = now()->weekOfYear;
        $weekCycle = $weekNumber % 2 === 1 ? 1 : 2; // Odd weeks are cycle 1, even weeks are cycle 2
        
        // Get today's menu items from cook's planning
        $todayMenuItems = \App\Models\Menu::where('day', $currentDay)
            ->where('week_cycle', $weekCycle)
            ->get()
            ->groupBy('meal_type');
            
        // Get pre-orders for today to prepare correct quantities
        $preOrders = \App\Models\PreOrder::where('date', $today)
            ->where('is_attending', true)
            ->get()
            ->groupBy('meal_type');
        
        // Count expected attendance for each meal type
        $mealAttendance = [
            'breakfast' => $preOrders->get('breakfast') ? $preOrders->get('breakfast')->count() : 0,
            'lunch' => $preOrders->get('lunch') ? $preOrders->get('lunch')->count() : 0,
            'dinner' => $preOrders->get('dinner') ? $preOrders->get('dinner')->count() : 0,
        ];
        
        // Get inventory items needed for today's meals
        $requiredIngredients = [];
        foreach ($todayMenuItems as $mealType => $meals) {
            foreach ($meals as $meal) {
                // Parse ingredients from description
                $ingredientText = $meal->description;
                if (strpos($ingredientText, 'Ingredients:') !== false) {
                    $ingredientList = explode(':', $ingredientText)[1];
                    $ingredients = array_map('trim', explode(',', $ingredientList));
                    foreach ($ingredients as $ingredient) {
                        if (!isset($requiredIngredients[$ingredient])) {
                            $requiredIngredients[$ingredient] = 0;
                        }
                        $requiredIngredients[$ingredient] += $mealAttendance[$mealType] ?? 1;
                    }
                }
            }
        }
        
        return view('kitchen.daily-menu', [
            'todayMenuItems' => $todayMenuItems,
            'mealAttendance' => $mealAttendance,
            'requiredIngredients' => $requiredIngredients,
            'today' => $today,
            'weekCycle' => $weekCycle,
            'currentDay' => $currentDay
        ]);
    }
}