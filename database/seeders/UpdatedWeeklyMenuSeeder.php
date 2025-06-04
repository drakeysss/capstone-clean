<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyMenu;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdatedWeeklyMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing weekly menu items
        DB::table('weekly_menus')->truncate();
        
        // Find admin user to set as creator
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;
        
        // Week 1 & 3 Menu Items
        $week1Menu = [
            'monday' => [
                'breakfast' => [
                    'name' => 'Chicken Loaf with Energen',
                    'description' => 'Chicken Loaf, Energen, Water',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Fried Fish',
                    'description' => 'Fish, Oil, Salt',
                    'price' => 80.00
                ],
                'dinner' => [
                    'name' => 'Ginisang Cabbage',
                    'description' => 'Cabbage, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ],
            'tuesday' => [
                'breakfast' => [
                    'name' => 'Odong with Sardines',
                    'description' => 'Odong Noodles, Sardines, Water',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Fried Chicken',
                    'description' => 'Chicken, Oil, Salt, Pepper',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Baguio Beans',
                    'description' => 'Baguio Beans, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ],
            'wednesday' => [
                'breakfast' => [
                    'name' => 'Hotdogs',
                    'description' => 'Hotdogs, Oil',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Porkchop Guisado',
                    'description' => 'Porkchop, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Eggplant with Eggs',
                    'description' => 'Eggplant, Eggs, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'thursday' => [
                'breakfast' => [
                    'name' => 'Boiled Eggs with Energen',
                    'description' => 'Eggs, Energen, Water',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Groundpork',
                    'description' => 'Ground Pork, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Chopsuey',
                    'description' => 'Mixed Vegetables, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'friday' => [
                'breakfast' => [
                    'name' => 'Ham',
                    'description' => 'Ham, Oil',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Fried Chicken',
                    'description' => 'Chicken, Oil, Salt, Pepper',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Monggo Beans',
                    'description' => 'Monggo Beans, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ],
            'saturday' => [
                'breakfast' => [
                    'name' => 'Sardines with Eggs',
                    'description' => 'Sardines, Eggs, Oil',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Burger Steak',
                    'description' => 'Burger Patty, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Utan Bisaya with Buwad',
                    'description' => 'Mixed Vegetables, Buwad, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'sunday' => [
                'breakfast' => [
                    'name' => 'Tomato with Eggs',
                    'description' => 'Tomatoes, Eggs, Garlic, Onion, Oil, Salt',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Fried Fish',
                    'description' => 'Fish, Oil, Salt',
                    'price' => 80.00
                ],
                'dinner' => [
                    'name' => 'Sari-Sari',
                    'description' => 'Mixed Vegetables, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ]
        ];
        
        // Week 2 & 4 Menu Items
        $week2Menu = [
            'monday' => [
                'breakfast' => [
                    'name' => 'Chorizo',
                    'description' => 'Chorizo, Oil',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Chicken Adobo',
                    'description' => 'Chicken, Soy Sauce, Vinegar, Garlic, Onion',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'String Beans Guisado',
                    'description' => 'String Beans, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ],
            'tuesday' => [
                'breakfast' => [
                    'name' => 'Scrambled Eggs with Energen',
                    'description' => 'Eggs, Energen, Water',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Fried Fish',
                    'description' => 'Fish, Oil, Salt',
                    'price' => 80.00
                ],
                'dinner' => [
                    'name' => 'Talong with Eggs',
                    'description' => 'Eggplant, Eggs, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'wednesday' => [
                'breakfast' => [
                    'name' => 'Sardines with Eggs',
                    'description' => 'Sardines, Eggs, Oil',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Groundpork',
                    'description' => 'Ground Pork, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Tinun-ang Kalabasa with Buwad',
                    'description' => 'Kalabasa, Buwad, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'thursday' => [
                'breakfast' => [
                    'name' => 'Luncheon Meat',
                    'description' => 'Luncheon Meat, Oil',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Fried Chicken',
                    'description' => 'Chicken, Oil, Salt, Pepper',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Chopsuey',
                    'description' => 'Mixed Vegetables, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'friday' => [
                'breakfast' => [
                    'name' => 'Sotanghon Guisado',
                    'description' => 'Sotanghon, Garlic, Onion, Oil, Salt',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Pork Menudo',
                    'description' => 'Pork, Carrots, Potatoes, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Monggo Beans',
                    'description' => 'Monggo Beans, Garlic, Onion, Oil, Salt',
                    'price' => 70.00
                ]
            ],
            'saturday' => [
                'breakfast' => [
                    'name' => 'Hotdogs',
                    'description' => 'Hotdogs, Oil',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Meatballs',
                    'description' => 'Meatballs, Garlic, Onion, Oil, Salt',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Utan Bisaya with Buwad',
                    'description' => 'Mixed Vegetables, Buwad, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ],
            'sunday' => [
                'breakfast' => [
                    'name' => 'Ampalaya with Eggs with Energen',
                    'description' => 'Ampalaya, Eggs, Energen, Water',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Fried Fish',
                    'description' => 'Fish, Oil, Salt',
                    'price' => 80.00
                ],
                'dinner' => [
                    'name' => 'Pakbit',
                    'description' => 'Pakbit, Garlic, Onion, Oil, Salt',
                    'price' => 75.00
                ]
            ]
        ];
        
        // Create Week 1 & 3 Menu Items
        foreach ($week1Menu as $day => $meals) {
            foreach ($meals as $mealType => $mealDetails) {
                WeeklyMenu::create([
                    'name' => $mealDetails['name'],
                    'description' => $mealDetails['description'],
                    'day_of_week' => $day,
                    'meal_type' => $mealType,
                    'week_cycle' => 1,
                    'price' => $mealDetails['price'],
                    'is_available' => true,
                    'created_by' => $adminId
                ]);
            }
        }
        
        // Create Week 2 & 4 Menu Items
        foreach ($week2Menu as $day => $meals) {
            foreach ($meals as $mealType => $mealDetails) {
                WeeklyMenu::create([
                    'name' => $mealDetails['name'],
                    'description' => $mealDetails['description'],
                    'day_of_week' => $day,
                    'meal_type' => $mealType,
                    'week_cycle' => 2,
                    'price' => $mealDetails['price'],
                    'is_available' => true,
                    'created_by' => $adminId
                ]);
            }
        }
    }
}
