<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MenuController extends Controller
{
    public function index()
    {
        $menus = [
            1 => [], // Week 1 & 3
            2 => []  // Week 2 & 4
        ];

        // Initialize the structure for both cycles
        foreach ([1, 2] as $cycle) {
            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                $menus[$cycle][$day] = [
                    'breakfast' => ['name' => '', 'ingredients' => ''],
                    'lunch' => ['name' => '', 'ingredients' => ''],
                    'dinner' => ['name' => '', 'ingredients' => '']
                ];
            }
        }

        // Get all menus from database
        $allMenus = Menu::all();

        // Populate the menus array with actual data
        foreach ($allMenus as $menu) {
            if (isset($menu->week_cycle) && isset($menu->day) && isset($menu->meal_type)) {
                $menus[$menu->week_cycle][$menu->day][$menu->meal_type] = [
                    'name' => $menu->name,
                    'ingredients' => $menu->description
                ];
            }
        }

        return view('cook.menu', compact('menus'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string',
            'meal_type' => 'required|string',
            'cycle' => 'required|numeric',
            'name' => 'required|string',
            'ingredients' => 'required|string'
        ]);

        $menu = Menu::updateOrCreate(
            [
                'day' => $validated['day'],
                'meal_type' => $validated['meal_type'],
                'week_cycle' => $validated['cycle']
            ],
            [
                'name' => $validated['name'],
                'description' => $validated['ingredients'],
                'category' => 'regular',
                'is_available' => true,
                'date' => Carbon::now()
            ]
        );

        return response()->json(['success' => true, 'menu' => $menu]);
    }
}
