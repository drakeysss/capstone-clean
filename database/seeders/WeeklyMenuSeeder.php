<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyMenu;
use App\Models\User;
use Carbon\Carbon;

class WeeklyMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find admin user to set as creator
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;
        
        // Get the start of the current week
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        
        // Week 1 & 3 Menu Items
        $week1Menu = [
            'monday' => [
                'breakfast' => [
                    'name' => 'Boiled Eggs with Energen',
                    'description' => 'Nutritious breakfast to start your day',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Pork Adobo with Rice',
                    'description' => 'Classic Filipino dish with rice',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Chicken Tinola with Rice',
                    'description' => 'Healthy soup with ginger and vegetables',
                    'price' => 85.00
                ]
            ],
            'tuesday' => [
                'breakfast' => [
                    'name' => 'Hotdog with Fried Rice',
                    'description' => 'Simple and filling breakfast',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Beef Caldereta with Rice',
                    'description' => 'Spicy beef stew with vegetables',
                    'price' => 90.00
                ],
                'dinner' => [
                    'name' => 'Fish Fillet with Rice',
                    'description' => 'Light and healthy dinner option',
                    'price' => 80.00
                ]
            ],
            'wednesday' => [
                'breakfast' => [
                    'name' => 'Pancakes with Coffee',
                    'description' => 'Sweet start to your day',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Chicken Curry with Rice',
                    'description' => 'Flavorful curry dish',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Pork Sinigang with Rice',
                    'description' => 'Sour soup with vegetables',
                    'price' => 85.00
                ]
            ],
            'thursday' => [
                'breakfast' => [
                    'name' => 'Boiled Eggs with Energen',
                    'description' => 'Nutritious breakfast to start your day',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Beef Nilaga with Rice',
                    'description' => 'Hearty beef soup with vegetables',
                    'price' => 90.00
                ],
                'dinner' => [
                    'name' => 'Chicken Afritada with Rice',
                    'description' => 'Tomato-based chicken stew',
                    'price' => 85.00
                ]
            ],
            'friday' => [
                'breakfast' => [
                    'name' => 'Corned Beef with Rice',
                    'description' => 'Classic Filipino breakfast',
                    'price' => 70.00
                ],
                'lunch' => [
                    'name' => 'Sweet and Sour Fish with Rice',
                    'description' => 'Tangy fish dish with vegetables',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Pork Menudo with Rice',
                    'description' => 'Tomato-based pork stew with vegetables',
                    'price' => 85.00
                ]
            ],
            'saturday' => [
                'breakfast' => [
                    'name' => 'Spam with Fried Rice',
                    'description' => 'Satisfying breakfast option',
                    'price' => 75.00
                ],
                'lunch' => [
                    'name' => 'Chicken Inasal with Rice',
                    'description' => 'Grilled chicken with special marinade',
                    'price' => 90.00
                ],
                'dinner' => [
                    'name' => 'Beef Tapa with Rice',
                    'description' => 'Filipino-style beef jerky with rice',
                    'price' => 90.00
                ]
            ],
            'sunday' => [
                'breakfast' => [
                    'name' => 'Tomato with Eggs',
                    'description' => 'Light and healthy breakfast',
                    'price' => 55.00
                ],
                'lunch' => [
                    'name' => 'Lechon Kawali with Rice',
                    'description' => 'Crispy pork belly with rice',
                    'price' => 95.00
                ],
                'dinner' => [
                    'name' => 'Chicken Sopas',
                    'description' => 'Creamy chicken soup with macaroni',
                    'price' => 75.00
                ]
            ]
        ];
        
        // Create Week 1 & 3 Menu Items
        foreach ($week1Menu as $day => $meals) {
            $dayNumber = $this->getDayNumber($day);
            $date = $weekStart->copy()->addDays($dayNumber);
            
            foreach ($meals as $mealType => $mealDetails) {
                WeeklyMenu::create([
                    'name' => $mealDetails['name'],
                    'description' => $mealDetails['description'],
                    'day_of_week' => $day,
                    'meal_type' => $mealType,
                    'price' => $mealDetails['price'],
                    'is_available' => true,
                    'created_by' => $adminId,
                    'week_cycle' => 1 // Week 1 & 3
                ]);
            }
        }
        
        // Week 2 & 4 Menu Items
        $week2Menu = [
            'monday' => [
                'breakfast' => [
                    'name' => 'Longganisa with Fried Rice',
                    'description' => 'Filipino sweet sausage with rice',
                    'price' => 70.00
                ],
                'lunch' => [
                    'name' => 'Beef Steak with Rice',
                    'description' => 'Tender beef slices with onions',
                    'price' => 90.00
                ],
                'dinner' => [
                    'name' => 'Chicken Adobo with Rice',
                    'description' => 'Classic Filipino dish with vinegar and soy sauce',
                    'price' => 85.00
                ]
            ],
            'tuesday' => [
                'breakfast' => [
                    'name' => 'Daing na Bangus with Rice',
                    'description' => 'Marinated milkfish with garlic rice',
                    'price' => 75.00
                ],
                'lunch' => [
                    'name' => 'Pork Binagoongan with Rice',
                    'description' => 'Pork with shrimp paste',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Beef Bulalo',
                    'description' => 'Bone marrow soup with vegetables',
                    'price' => 95.00
                ]
            ],
            'wednesday' => [
                'breakfast' => [
                    'name' => 'Tocino with Fried Rice',
                    'description' => 'Sweet cured pork with rice',
                    'price' => 70.00
                ],
                'lunch' => [
                    'name' => 'Chicken Pastel with Rice',
                    'description' => 'Creamy chicken stew with vegetables',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Pork Sinigang with Rice',
                    'description' => 'Sour soup with vegetables',
                    'price' => 85.00
                ]
            ],
            'thursday' => [
                'breakfast' => [
                    'name' => 'Luncheon Meat',
                    'description' => 'Simple breakfast meat with rice',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Chicken Afritada with Rice',
                    'description' => 'Tomato-based chicken stew',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Beef Kare-Kare with Rice',
                    'description' => 'Peanut-based stew with vegetables',
                    'price' => 95.00
                ]
            ],
            'friday' => [
                'breakfast' => [
                    'name' => 'Tuna Omelette with Rice',
                    'description' => 'Protein-rich breakfast',
                    'price' => 65.00
                ],
                'lunch' => [
                    'name' => 'Pork BBQ with Rice',
                    'description' => 'Grilled marinated pork skewers',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Fish Sarciado with Rice',
                    'description' => 'Fish in tomato and egg sauce',
                    'price' => 80.00
                ]
            ],
            'saturday' => [
                'breakfast' => [
                    'name' => 'Beef Tapa with Rice',
                    'description' => 'Filipino-style beef jerky with rice',
                    'price' => 75.00
                ],
                'lunch' => [
                    'name' => 'Chicken Tinola with Rice',
                    'description' => 'Healthy soup with ginger and vegetables',
                    'price' => 85.00
                ],
                'dinner' => [
                    'name' => 'Pork Sisig with Rice',
                    'description' => 'Sizzling chopped pork dish',
                    'price' => 90.00
                ]
            ],
            'sunday' => [
                'breakfast' => [
                    'name' => 'Ampalaya with Eggs with Energen',
                    'description' => 'Healthy bitter melon with eggs',
                    'price' => 60.00
                ],
                'lunch' => [
                    'name' => 'Beef Caldereta with Rice',
                    'description' => 'Spicy beef stew with vegetables',
                    'price' => 90.00
                ],
                'dinner' => [
                    'name' => 'Chicken Sotanghon Soup',
                    'description' => 'Glass noodle soup with chicken',
                    'price' => 75.00
                ]
            ]
        ];
        
        // Create Week 2 & 4 Menu Items
        foreach ($week2Menu as $day => $meals) {
            $dayNumber = $this->getDayNumber($day);
            $date = $weekStart->copy()->addDays($dayNumber + 7); // Add 7 days for week 2
            
            foreach ($meals as $mealType => $mealDetails) {
                WeeklyMenu::create([
                    'name' => $mealDetails['name'],
                    'description' => $mealDetails['description'],
                    'day_of_week' => $day,
                    'meal_type' => $mealType,
                    'price' => $mealDetails['price'],
                    'is_available' => true,
                    'created_by' => $adminId,
                    'week_cycle' => 2 // Week 2 & 4
                ]);
            }
        }
    }
    
    /**
     * Get the day number (0 for Monday, 6 for Sunday)
     */
    private function getDayNumber($day)
    {
        $days = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6
        ];
        
        return $days[$day] ?? 0;
    }
}
