<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Create sample menus for the current week
            $today = Carbon::today();
            $weekStart = $today->copy()->startOfWeek();
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            // Create breakfast menus
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i);
                Menu::create([
                    'name' => 'Breakfast Option 1',
                    'description' => 'A nutritious breakfast to start your day',
                    'category' => 'Main',
                    'meal_type' => 'breakfast',
                    'date' => $date->format('Y-m-d'),
                    'price' => 5.99,
                    'is_available' => true,
                    'created_by' => 1, // Assuming admin user has ID 1
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
                
                Menu::create([
                    'name' => 'Breakfast Option 2',
                    'description' => 'A lighter breakfast option',
                    'category' => 'Main',
                    'meal_type' => 'breakfast',
                    'date' => $date->format('Y-m-d'),
                    'price' => 4.99,
                    'is_available' => true,
                    'created_by' => 1,
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
            }
            
            // Create lunch menus
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i);
                Menu::create([
                    'name' => 'Lunch Option 1',
                    'description' => 'A hearty lunch meal',
                    'category' => 'Main',
                    'meal_type' => 'lunch',
                    'date' => $date->format('Y-m-d'),
                    'price' => 7.99,
                    'is_available' => true,
                    'created_by' => 1,
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
                
                Menu::create([
                    'name' => 'Lunch Option 2',
                    'description' => 'A vegetarian lunch option',
                    'category' => 'Main',
                    'meal_type' => 'lunch',
                    'date' => $date->format('Y-m-d'),
                    'price' => 6.99,
                    'is_available' => true,
                    'created_by' => 1,
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
            }
            
            // Create dinner menus
            for ($i = 0; $i < 7; $i++) {
                $date = $weekStart->copy()->addDays($i);
                Menu::create([
                    'name' => 'Dinner Option 1',
                    'description' => 'A delicious dinner meal',
                    'category' => 'Main',
                    'meal_type' => 'dinner',
                    'date' => $date->format('Y-m-d'),
                    'price' => 8.99,
                    'is_available' => true,
                    'created_by' => 1,
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
                
                Menu::create([
                    'name' => 'Dinner Option 2',
                    'description' => 'A lighter dinner option',
                    'category' => 'Main',
                    'meal_type' => 'dinner',
                    'date' => $date->format('Y-m-d'),
                    'price' => 7.99,
                    'is_available' => true,
                    'created_by' => 1,
                    'week_cycle' => 1,
                    'day' => $days[$i],
                ]);
            }

            $this->command->info('Menus seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding menus: ' . $e->getMessage());
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
