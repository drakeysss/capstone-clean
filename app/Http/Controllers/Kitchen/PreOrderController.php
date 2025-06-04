<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PreOrderController extends Controller
{
    /**
     * Display a listing of pre-orders for the kitchen team.
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
            
        // Get preparation status for each menu item
        $preparationStatus = PreOrder::where('date', $date)
            ->where('meal_type', $mealType)
            ->select('menu_id', 'is_prepared')
            ->groupBy('menu_id', 'is_prepared')
            ->get()
            ->groupBy('menu_id')
            ->map(function ($items) {
                return $items->pluck('is_prepared')->contains(true);
            });
            
        // Get upcoming dates with menus for the filter
        $upcomingDates = Menu::select('date')
            ->distinct()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->limit(14)
            ->pluck('date');
            
        return view('kitchen.pre-orders', compact(
            'menuItems', 
            'preOrderCounts', 
            'preparationStatus',
            'date', 
            'mealType', 
            'upcomingDates'
        ));
    }
    
    /**
     * Mark menu items as prepared.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markPrepared(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'menu_ids' => 'required|array',
            'menu_ids.*' => 'exists:menus,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update preparation status for the selected menu items
        foreach ($request->menu_ids as $menuId) {
            PreOrder::where('date', $request->date)
                ->where('meal_type', $request->meal_type)
                ->where('menu_id', $menuId)
                ->update(['is_prepared' => true]);
        }

        return redirect()->route('kitchen.pre-orders', ['date' => $request->date, 'meal_type' => $request->meal_type])
            ->with('success', 'Menu items marked as prepared successfully.');
    }
}
