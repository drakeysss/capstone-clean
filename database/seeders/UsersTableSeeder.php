<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Clear existing users
            DB::table('users')->truncate();
            
            // Create admin user
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            // Create cook users
            $cooks = [
                [
                    'name' => 'Cristina Manlunas',
                    'email' => 'cook1@example.com',
                    'password' => 'cook123'
                ],
                [
                    'name' => 'John Smith',
                    'email' => 'cook2@example.com',
                    'password' => 'cook123'
                ]
            ];

            foreach ($cooks as $cook) {
                User::create([
                    'name' => $cook['name'],
                    'email' => $cook['email'],
                    'password' => $cook['password'],
                    'role' => 'cook',
                    'email_verified_at' => now(),
                ]);
            }

            // Create kitchen team users
            $kitchenStaff = [
                [
                    'name' => 'Maria Santos',
                    'email' => 'kitchen1@example.com',
                    'password' => 'kitchen123'
                ],
                [
                    'name' => 'Robert Johnson',
                    'email' => 'kitchen2@example.com',
                    'password' => 'kitchen123'
                ]
            ];

            foreach ($kitchenStaff as $staff) {
                User::create([
                    'name' => $staff['name'],
                    'email' => $staff['email'],
                    'password' => $staff['password'],
                    'role' => 'kitchen',
                    'email_verified_at' => now(),
                ]);
            }

            // Create student users
            $students = [
                [
                    'name' => 'Jasper Drake',
                    'email' => 'student1@example.com',
                    'password' => 'student123'
                ],
                [
                    'name' => 'Emma Wilson',
                    'email' => 'student2@example.com',
                    'password' => 'student123'
                ],
                [
                    'name' => 'Michael Brown',
                    'email' => 'student3@example.com',
                    'password' => 'student123'
                ],
                [
                    'name' => 'Sarah Davis',
                    'email' => 'student4@example.com',
                    'password' => 'student123'
                ]
            ];

            foreach ($students as $student) {
                User::create([
                    'name' => $student['name'],
                    'email' => $student['email'],
                    'password' => $student['password'],
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);
            }

            $this->command->info('Users seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding users: ' . $e->getMessage());
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
