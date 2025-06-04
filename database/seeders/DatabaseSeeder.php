<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            MenusTableSeeder::class,
            UpdatedWeeklyMenuSeeder::class,
            InventorySeeder::class,
            AnnouncementsSeeder::class,
            MenuSeeder::class
            // Add other seeders here as needed
        ]);
    }
}
