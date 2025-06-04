<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PreOrdersExport;

class PreOrderController extends Controller
{
    /**
     * Display a listing of pre-orders for the cook/admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $mealType = $request->input('meal_type', 'lunch');
        
        // Get the menu items for the selected date and meal type
        $menuItems = Menu::where('date', $date)
            ->where('meal_type', $mealType)
            ->get();
            
        // Get pre-order counts for each menu item
        $preOrderCounts = PreOrder::where('date', $date)
            ->where('meal_type', $mealType)
            ->select('menu_id', DB::raw('count(*) as total_orders'))
            ->groupBy('menu_id')
            ->pluck('total_orders', 'menu_id')
            ->toArray();
            
        // Get total student count
        $totalStudents = User::where('role', 'student')->count();
        
        // Calculate participation percentage
        $totalPreOrders = array_sum($preOrderCounts);
        $participationPercentage = $totalStudents > 0 ? ($totalPreOrders / $totalStudents) * 100 : 0;
        
        // Get upcoming dates with menus for the filter
        $upcomingDates = Menu::select('date')
            ->distinct()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->limit(14)
            ->pluck('date');
            
        return view('cook.pre-orders', compact(
            'menuItems', 
            'preOrderCounts', 
            'totalStudents', 
            'participationPercentage', 
            'date', 
            'mealType', 
            'upcomingDates'
        ));
    }
    
    /**
     * Export pre-orders to Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $mealType = $request->input('meal_type', 'lunch');
        
        return Excel::download(new PreOrdersExport($date, $mealType), "pre-orders-{$date}-{$mealType}.xlsx");
    }
}
