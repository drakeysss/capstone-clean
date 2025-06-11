<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Only proceed if weekly_menus table exists
        if (Schema::hasTable('weekly_menus')) {
            // First, ensure all weekly_menus data is copied to menus table
            $weeklyMenus = DB::table('weekly_menus')->get();
            
            foreach ($weeklyMenus as $weeklyMenu) {
                // Check if a menu already exists for this week cycle, day, and meal type
                $existingMenu = DB::table('menus')
                    ->where('week_cycle', $weeklyMenu->week_cycle)
                    ->where('day', ucfirst($weeklyMenu->day_of_week))
                    ->where('meal_type', $weeklyMenu->meal_type)
                    ->first();
                
                if (!$existingMenu) {
                    // Create new menu entry
                    DB::table('menus')->insert([
                        'name' => $weeklyMenu->name,
                        'description' => $weeklyMenu->description,
                        'category' => 'regular',
                        'meal_type' => $weeklyMenu->meal_type,
                        'date' => now(), // This will be updated by the menu management system
                        'price' => $weeklyMenu->price,
                        'is_available' => $weeklyMenu->is_available,
                        'week_cycle' => $weeklyMenu->week_cycle,
                        'day' => ucfirst($weeklyMenu->day_of_week),
                        'created_by' => $weeklyMenu->created_by,
                        'created_at' => $weeklyMenu->created_at,
                        'updated_at' => $weeklyMenu->updated_at
                    ]);
                }
            }
            
            // Drop the weekly_menus table
            Schema::dropIfExists('weekly_menus');
        }
    }

    public function down()
    {
        // Recreate weekly_menus table
        Schema::create('weekly_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('day_of_week');
            $table->string('meal_type');
            $table->unsignedTinyInteger('week_cycle');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_available')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('day_of_week');
            $table->index('meal_type');
            $table->index('week_cycle');
        });
        
        // Copy data back from menus to weekly_menus
        $menus = DB::table('menus')->get();
        
        foreach ($menus as $menu) {
            DB::table('weekly_menus')->insert([
                'name' => $menu->name,
                'description' => $menu->description,
                'day_of_week' => strtolower($menu->day),
                'meal_type' => $menu->meal_type,
                'week_cycle' => $menu->week_cycle,
                'price' => $menu->price,
                'is_available' => $menu->is_available,
                'created_by' => $menu->created_by,
                'created_at' => $menu->created_at,
                'updated_at' => $menu->updated_at
            ]);
        }
    }
}; 