<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index()
    {
        // Initialize empty menus array for both cycles
        $menus = [
            1 => [], // Week 1 & 3
            2 => []  // Week 2 & 4
        ];

        // Get all menus from database, explicitly grouped by cycle
        $cycle1Menus = Menu::where('week_cycle', 1)->get();
        $cycle2Menus = Menu::where('week_cycle', 2)->get();

        // Initialize both cycles with empty structure
        foreach ([1, 2] as $cycle) {
            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                $menus[$cycle][$day] = [
                    'breakfast' => ['name' => 'Not set', 'ingredients' => ''],
                    'lunch' => ['name' => 'Not set', 'ingredients' => ''],
                    'dinner' => ['name' => 'Not set', 'ingredients' => '']
                ];
            }
        }

        // Fill in cycle 1 menu items
        foreach ($cycle1Menus as $menu) {
            if ($menu->day && $menu->meal_type) {
                $menus[1][$menu->day][$menu->meal_type] = [
                    'name' => $menu->name,
                    'ingredients' => $menu->description
                ];
            }
        }

        // Fill in cycle 2 menu items
        foreach ($cycle2Menus as $menu) {
            if ($menu->day && $menu->meal_type) {
                $menus[2][$menu->day][$menu->meal_type] = [
                    'name' => $menu->name,
                    'ingredients' => $menu->description
                ];
            }
        }

        return view('cook.menu', compact('menus'));
    }

    public function create()
    {
        return view('cook.menu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'ingredients' => 'required|array',
            'ingredients.*.item_id' => 'required|exists:inventory,id',
            'ingredients.*.quantity' => 'required|numeric|min:0'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu-images', 'public');
            $validated['image'] = $imagePath;
        }

        $menu = Menu::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'image' => $validated['image'] ?? null
        ]);

        foreach ($validated['ingredients'] as $ingredient) {
            $menu->ingredients()->create([
                'inventory_item_id' => $ingredient['item_id'],
                'quantity_required' => $ingredient['quantity']
            ]);
        }

        return redirect()->route('cook.menu.index')->with('success', 'Menu item created successfully');
    }

    public function edit(Menu $menu)
    {
        $menu->load('ingredients');
        return view('cook.menu.edit', compact('menu'));
    }

    public function update(Request $request)
    {
        try {
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
                    'price' => 0.00,
                    'is_available' => true
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully',
                'menu' => $menu
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image) {
            \Storage::disk('public')->delete($menu->image);
        }
        $menu->ingredients()->delete();
        $menu->delete();
        return redirect()->route('cook.menu.index')->with('success', 'Menu item deleted successfully');
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        return redirect()->route('cook.menu.index')->with('success', 'Menu availability updated');
    }
}

