<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Common ingredients used in your menu items
        $inventoryItems = [
            [
                'item_name' => 'Rice',
                'quantity' => 50.0,
                'unit' => 'kg',
                'category' => 'Grains',
                'expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'minimum_stock' => 10.0
            ],
            [
                'item_name' => 'Chicken',
                'quantity' => 30.0,
                'unit' => 'kg',
                'category' => 'Meat',
                'expiry_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'minimum_stock' => 5.0
            ],
            [
                'item_name' => 'Eggs',
                'quantity' => 100.0,
                'unit' => 'pieces',
                'category' => 'Dairy',
                'expiry_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'minimum_stock' => 20.0
            ],
            [
                'item_name' => 'Fish',
                'quantity' => 15.0,
                'unit' => 'kg',
                'category' => 'Seafood',
                'expiry_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'minimum_stock' => 3.0
            ],
            [
                'item_name' => 'Pork',
                'quantity' => 25.0,
                'unit' => 'kg',
                'category' => 'Meat',
                'expiry_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'minimum_stock' => 5.0
            ],
            [
                'item_name' => 'Garlic',
                'quantity' => 5.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'minimum_stock' => 1.0
            ],
            [
                'item_name' => 'Onion',
                'quantity' => 10.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'Cooking Oil',
                'quantity' => 20.0,
                'unit' => 'liters',
                'category' => 'Cooking Supplies',
                'expiry_date' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                'minimum_stock' => 5.0
            ],
            [
                'item_name' => 'Salt',
                'quantity' => 10.0,
                'unit' => 'kg',
                'category' => 'Seasonings',
                'expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'Pepper',
                'quantity' => 3.0,
                'unit' => 'kg',
                'category' => 'Seasonings',
                'expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'),
                'minimum_stock' => 0.5
            ],
            [
                'item_name' => 'Cabbage',
                'quantity' => 15.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'minimum_stock' => 3.0
            ],
            [
                'item_name' => 'Tomatoes',
                'quantity' => 10.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'Eggplant',
                'quantity' => 8.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'String Beans',
                'quantity' => 7.0,
                'unit' => 'kg',
                'category' => 'Vegetables',
                'expiry_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'minimum_stock' => 1.5
            ],
            [
                'item_name' => 'Monggo Beans',
                'quantity' => 10.0,
                'unit' => 'kg',
                'category' => 'Legumes',
                'expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'Chicken Loaf',
                'quantity' => 10.0,
                'unit' => 'kg',
                'category' => 'Processed Meat',
                'expiry_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'minimum_stock' => 2.0
            ],
            [
                'item_name' => 'Energen',
                'quantity' => 50.0,
                'unit' => 'sachets',
                'category' => 'Beverages',
                'expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'minimum_stock' => 10.0
            ],
            [
                'item_name' => 'Sardines',
                'quantity' => 30.0,
                'unit' => 'cans',
                'category' => 'Canned Goods',
                'expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'),
                'minimum_stock' => 10.0
            ],
            [
                'item_name' => 'Hotdogs',
                'quantity' => 20.0,
                'unit' => 'kg',
                'category' => 'Processed Meat',
                'expiry_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'minimum_stock' => 5.0
            ],
            [
                'item_name' => 'Ham',
                'quantity' => 15.0,
                'unit' => 'kg',
                'category' => 'Processed Meat',
                'expiry_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'minimum_stock' => 3.0
            ]
        ];

        // Insert inventory items
        foreach ($inventoryItems as $item) {
            DB::table('inventory')->insert([
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'category' => $item['category'],
                'expiry_date' => $item['expiry_date'],
                'minimum_stock' => $item['minimum_stock'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
