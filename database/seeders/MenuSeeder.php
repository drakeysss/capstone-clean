<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $weeklyMenus = [
            // Week 1 & 3
            [
                'monday' => [
                    ['name' => 'Chicken Loaf with Energen', 'meal_type' => 'breakfast', 'ingredients' => ['Chicken Loaf', 'Energen', 'Water']],
                    ['name' => 'Fried Fish', 'meal_type' => 'lunch', 'ingredients' => ['Fish', 'Oil', 'Salt']],
                    ['name' => 'Ginisang Cabbage', 'meal_type' => 'dinner', 'ingredients' => ['Cabbage', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'tuesday' => [
                    ['name' => 'Odong with Sardines', 'meal_type' => 'breakfast', 'ingredients' => ['Odong Noodles', 'Sardines', 'Water']],
                    ['name' => 'Fried Chicken', 'meal_type' => 'lunch', 'ingredients' => ['Chicken', 'Oil', 'Salt', 'Pepper']],
                    ['name' => 'Baguio Beans', 'meal_type' => 'dinner', 'ingredients' => ['Baguio Beans', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'wednesday' => [
                    ['name' => 'Hotdogs', 'meal_type' => 'breakfast', 'ingredients' => ['Hotdogs', 'Oil']],
                    ['name' => 'Porkchop Guisado', 'meal_type' => 'lunch', 'ingredients' => ['Porkchop', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Eggplant with Eggs', 'meal_type' => 'dinner', 'ingredients' => ['Eggplant', 'Eggs', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'thursday' => [
                    ['name' => 'Boiled Eggs with Energen', 'meal_type' => 'breakfast', 'ingredients' => ['Eggs', 'Energen', 'Water']],
                    ['name' => 'Groundpork', 'meal_type' => 'lunch', 'ingredients' => ['Ground Pork', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Chopsuey', 'meal_type' => 'dinner', 'ingredients' => ['Mixed Vegetables', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'friday' => [
                    ['name' => 'Ham', 'meal_type' => 'breakfast', 'ingredients' => ['Ham', 'Oil']],
                    ['name' => 'Fried Chicken', 'meal_type' => 'lunch', 'ingredients' => ['Chicken', 'Oil', 'Salt', 'Pepper']],
                    ['name' => 'Monggo Beans', 'meal_type' => 'dinner', 'ingredients' => ['Monggo Beans', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'saturday' => [
                    ['name' => 'Sardines with Eggs', 'meal_type' => 'breakfast', 'ingredients' => ['Sardines', 'Eggs', 'Oil']],
                    ['name' => 'Burger Steak', 'meal_type' => 'lunch', 'ingredients' => ['Burger Patty', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Utan Bisaya with Buwad', 'meal_type' => 'dinner', 'ingredients' => ['Mixed Vegetables', 'Buwad', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'sunday' => [
                    ['name' => 'Tomato with Eggs', 'meal_type' => 'breakfast', 'ingredients' => ['Tomatoes', 'Eggs', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Fried Fish', 'meal_type' => 'lunch', 'ingredients' => ['Fish', 'Oil', 'Salt']],
                    ['name' => 'Sari-Sari', 'meal_type' => 'dinner', 'ingredients' => ['Mixed Vegetables', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ]
            ],
            // Week 2 & 4
            [
                'monday' => [
                    ['name' => 'Chorizo', 'meal_type' => 'breakfast', 'ingredients' => ['Chorizo', 'Oil']],
                    ['name' => 'Chicken Adobo', 'meal_type' => 'lunch', 'ingredients' => ['Chicken', 'Soy Sauce', 'Vinegar', 'Garlic', 'Onion']],
                    ['name' => 'String Beans Guisado', 'meal_type' => 'dinner', 'ingredients' => ['String Beans', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'tuesday' => [
                    ['name' => 'Scrambled Eggs with Energen', 'meal_type' => 'breakfast', 'ingredients' => ['Eggs', 'Energen', 'Water']],
                    ['name' => 'Fried Fish', 'meal_type' => 'lunch', 'ingredients' => ['Fish', 'Oil', 'Salt']],
                    ['name' => 'Talong with Eggs', 'meal_type' => 'dinner', 'ingredients' => ['Eggplant', 'Eggs', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'wednesday' => [
                    ['name' => 'Sardines with Eggs', 'meal_type' => 'breakfast', 'ingredients' => ['Sardines', 'Eggs', 'Oil']],
                    ['name' => 'Groundpork', 'meal_type' => 'lunch', 'ingredients' => ['Ground Pork', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Tinun-ang Kalabasa with Buwad', 'meal_type' => 'dinner', 'ingredients' => ['Kalabasa', 'Buwad', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'thursday' => [
                    ['name' => 'Luncheon Meat', 'meal_type' => 'breakfast', 'ingredients' => ['Luncheon Meat', 'Oil']],
                    ['name' => 'Fried Chicken', 'meal_type' => 'lunch', 'ingredients' => ['Chicken', 'Oil', 'Salt', 'Pepper']],
                    ['name' => 'Chopsuey', 'meal_type' => 'dinner', 'ingredients' => ['Mixed Vegetables', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'friday' => [
                    ['name' => 'Sotanghon Guisado', 'meal_type' => 'breakfast', 'ingredients' => ['Sotanghon', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Pork Menudo', 'meal_type' => 'lunch', 'ingredients' => ['Pork', 'Carrots', 'Potatoes', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Monggo Beans', 'meal_type' => 'dinner', 'ingredients' => ['Monggo Beans', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'saturday' => [
                    ['name' => 'Hotdogs', 'meal_type' => 'breakfast', 'ingredients' => ['Hotdogs', 'Oil']],
                    ['name' => 'Meatballs', 'meal_type' => 'lunch', 'ingredients' => ['Meatballs', 'Garlic', 'Onion', 'Oil', 'Salt']],
                    ['name' => 'Utan Bisaya with Buwad', 'meal_type' => 'dinner', 'ingredients' => ['Mixed Vegetables', 'Buwad', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ],
                'sunday' => [
                    ['name' => 'Ampalaya with Eggs with Energen', 'meal_type' => 'breakfast', 'ingredients' => ['Ampalaya', 'Eggs', 'Energen', 'Water']],
                    ['name' => 'Fried Fish', 'meal_type' => 'lunch', 'ingredients' => ['Fish', 'Oil', 'Salt']],
                    ['name' => 'Pakbit', 'meal_type' => 'dinner', 'ingredients' => ['Pakbit', 'Garlic', 'Onion', 'Oil', 'Salt']]
                ]
            ]
        ];

        // Clear existing menus and their items
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('menu_items')->truncate();
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($weeklyMenus as $weekCycle => $days) {
            foreach ($days as $dayName => $meals) {
                foreach ($meals as $meal) {
                    $menu = Menu::create([
                        'name' => $meal['name'],
                        'description' => 'Ingredients: ' . implode(', ', $meal['ingredients']),
                        'category' => 'regular',
                        'meal_type' => $meal['meal_type'],
                        'date' => Carbon::parse('next ' . $dayName),
                        'price' => 0,
                        'is_available' => true,
                        'week_cycle' => $weekCycle + 1,
                        'day' => ucfirst($dayName)
                    ]);

                    \Log::info("Created menu item: {$meal['name']} for cycle " . ($weekCycle + 1) . " on {$dayName}");

                    // Create menu items for ingredients
                    foreach ($meal['ingredients'] as $ingredient) {
                        $inventoryItem = Inventory::firstOrCreate([
                            'name' => $ingredient
                        ], [
                            'description' => $ingredient,
                            'quantity' => 100,
                            'unit' => 'pieces',
                            'reorder_point' => 20
                        ]);

                        DB::table('menu_items')->insert([
                            'menu_id' => $menu->id,
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity_required' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            // No need to add a week since we're using week_cycle
        }
    }
}
