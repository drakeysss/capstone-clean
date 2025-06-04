<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnnouncementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample announcements
        $announcements = [
            [
                'title' => 'New Menu Items Available',
                'content' => 'We have added several new menu items for the upcoming week. Check out the weekly menu to see what\'s new!',
                'user_id' => 1, // Admin user
                'expiry_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'is_active' => true,
                'is_poll' => false,
                'poll_options' => null
            ],
            [
                'title' => 'Kitchen Closed for Maintenance',
                'content' => 'The kitchen will be closed for maintenance on June 5th. No meals will be served on this day. Please plan accordingly.',
                'user_id' => 1, // Admin user
                'expiry_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'is_active' => true,
                'is_poll' => false,
                'poll_options' => null
            ],
            [
                'title' => 'Meal Preference Poll',
                'content' => 'We want to know your preferences for next month\'s menu. Please vote for your favorite options!',
                'user_id' => 1, // Admin user
                'expiry_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'is_active' => true,
                'is_poll' => true,
                'poll_options' => json_encode([
                    'More vegetarian options',
                    'More seafood dishes',
                    'More local cuisine',
                    'More international dishes'
                ])
            ],
            [
                'title' => 'Special Meal for Graduation Day',
                'content' => 'We will be serving a special meal for graduation day on June 15th. Please pre-order to ensure availability.',
                'user_id' => 1, // Admin user
                'expiry_date' => Carbon::now()->addDays(21)->format('Y-m-d'),
                'is_active' => true,
                'is_poll' => false,
                'poll_options' => null
            ],
            [
                'title' => 'Budget Allocation Poll',
                'content' => 'How should we allocate our remaining budget for the semester? Please vote for your preference.',
                'user_id' => 1, // Admin user
                'expiry_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'is_active' => true,
                'is_poll' => true,
                'poll_options' => json_encode([
                    'Upgrade kitchen equipment',
                    'Purchase higher quality ingredients',
                    'Add more menu variety',
                    'Improve dining area'
                ])
            ]
        ];

        // Insert announcements
        foreach ($announcements as $announcement) {
            DB::table('announcements')->insert([
                'title' => $announcement['title'],
                'content' => $announcement['content'],
                'user_id' => $announcement['user_id'],
                'expiry_date' => $announcement['expiry_date'],
                'is_active' => $announcement['is_active'],
                'is_poll' => $announcement['is_poll'],
                'poll_options' => $announcement['poll_options'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
