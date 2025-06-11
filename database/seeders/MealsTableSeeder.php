<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meal;

class MealsTableSeeder extends Seeder
{
    public function run()
    {
        // Sample meals for Week 1 & 3
        $week1Meals = [
            // Monday
            [
                'name' => 'Continental Breakfast',
                'ingredients' => ['Bread', 'Butter', 'Jam', 'Coffee', 'Milk'],
                'prep_time' => 15,
                'cooking_time' => 5,
                'serving_size' => 100,
                'meal_type' => 'breakfast',
                'day_of_week' => 'monday',
                'week_cycle' => 1
            ],
            [
                'name' => 'Chicken Adobo',
                'ingredients' => ['Chicken', 'Soy Sauce', 'Vinegar', 'Garlic', 'Pepper'],
                'prep_time' => 20,
                'cooking_time' => 45,
                'serving_size' => 50,
                'meal_type' => 'lunch',
                'day_of_week' => 'monday',
                'week_cycle' => 1
            ],
            [
                'name' => 'Beef Steak',
                'ingredients' => ['Beef', 'Onions', 'Garlic', 'Soy Sauce', 'Pepper'],
                'prep_time' => 25,
                'cooking_time' => 30,
                'serving_size' => 50,
                'meal_type' => 'dinner',
                'day_of_week' => 'monday',
                'week_cycle' => 1
            ],
            // Tuesday
            [
                'name' => 'Pancakes with Syrup',
                'ingredients' => ['Flour', 'Eggs', 'Milk', 'Butter', 'Maple Syrup'],
                'prep_time' => 15,
                'cooking_time' => 20,
                'serving_size' => 100,
                'meal_type' => 'breakfast',
                'day_of_week' => 'tuesday',
                'week_cycle' => 1
            ],
            [
                'name' => 'Pork Sinigang',
                'ingredients' => ['Pork', 'Tamarind', 'Vegetables', 'Fish Sauce'],
                'prep_time' => 30,
                'cooking_time' => 60,
                'serving_size' => 50,
                'meal_type' => 'lunch',
                'day_of_week' => 'tuesday',
                'week_cycle' => 1
            ],
            [
                'name' => 'Chicken Curry',
                'ingredients' => ['Chicken', 'Coconut Milk', 'Curry Powder', 'Vegetables'],
                'prep_time' => 25,
                'cooking_time' => 45,
                'serving_size' => 50,
                'meal_type' => 'dinner',
                'day_of_week' => 'tuesday',
                'week_cycle' => 1
            ],
        ];

        // Sample meals for Week 2 & 4
        $week2Meals = [
            // Monday
            [
                'name' => 'Filipino Breakfast',
                'ingredients' => ['Rice', 'Eggs', 'Longganisa', 'Coffee'],
                'prep_time' => 20,
                'cooking_time' => 15,
                'serving_size' => 100,
                'meal_type' => 'breakfast',
                'day_of_week' => 'monday',
                'week_cycle' => 2
            ],
            [
                'name' => 'Beef Caldereta',
                'ingredients' => ['Beef', 'Tomato Sauce', 'Vegetables', 'Cheese'],
                'prep_time' => 30,
                'cooking_time' => 90,
                'serving_size' => 50,
                'meal_type' => 'lunch',
                'day_of_week' => 'monday',
                'week_cycle' => 2
            ],
            [
                'name' => 'Fish Fillet',
                'ingredients' => ['Fish', 'Bread Crumbs', 'Lemon', 'Herbs'],
                'prep_time' => 20,
                'cooking_time' => 25,
                'serving_size' => 50,
                'meal_type' => 'dinner',
                'day_of_week' => 'monday',
                'week_cycle' => 2
            ],
            // Tuesday
            [
                'name' => 'Oatmeal with Fruits',
                'ingredients' => ['Oats', 'Milk', 'Honey', 'Mixed Fruits'],
                'prep_time' => 10,
                'cooking_time' => 10,
                'serving_size' => 100,
                'meal_type' => 'breakfast',
                'day_of_week' => 'tuesday',
                'week_cycle' => 2
            ],
            [
                'name' => 'Chicken Tinola',
                'ingredients' => ['Chicken', 'Ginger', 'Green Papaya', 'Malunggay'],
                'prep_time' => 25,
                'cooking_time' => 45,
                'serving_size' => 50,
                'meal_type' => 'lunch',
                'day_of_week' => 'tuesday',
                'week_cycle' => 2
            ],
            [
                'name' => 'Pork Menudo',
                'ingredients' => ['Pork', 'Tomato Sauce', 'Vegetables', 'Raisins'],
                'prep_time' => 30,
                'cooking_time' => 60,
                'serving_size' => 50,
                'meal_type' => 'dinner',
                'day_of_week' => 'tuesday',
                'week_cycle' => 2
            ],
        ];

        // Insert all meals
        foreach (array_merge($week1Meals, $week2Meals) as $meal) {
            Meal::create($meal);
        }
    }
} 