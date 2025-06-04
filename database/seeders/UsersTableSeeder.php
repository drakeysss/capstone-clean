<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Instead of truncating, we'll update existing users or create new ones
        // We can't truncate due to foreign key constraints
        
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password123', // Will be hashed by the model's mutator
                'role' => 'admin',
            ]
        );

        // Create cook users if not exist
        $cooks = [
            [
                'name' => 'Cristina Manlunas',
                'email' => 'cook1@example.com',
                'password' => 'password123'
            ]
        ];

        foreach ($cooks as $cook) {
            User::updateOrCreate(
                ['email' => $cook['email']],
                [
                    'name' => $cook['name'],
                    'password' => $cook['password'], // Will be hashed by the model's mutator
                    'role' => 'cook'
                ]
            );
        }

        // Create kitchen team users if not exist
        $kitchenStaff = [
            [
                'name' => 'Maria Santos',
                'email' => 'kitchen1@example.com',
                'password' => 'password123'
            ]
        ];

        foreach ($kitchenStaff as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'password' => $staff['password'], // Will be hashed by the model's mutator
                    'role' => 'kitchen'
                ]
            );
        }

        // Create student users
        $students = [
            [
                'name' => 'Jasper Drake',
                'email' => 'student1@example.com',
                'password' => 'password123'
            ],
            // Add more students if needed
            [
                'name' => 'Student Test',
                'email' => 'student@example.com',
                'password' => 'password123'
            ]
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => $student['password'], // Will be hashed by the model's mutator
                    'role' => 'student'
                ]
            );
        }
    }
}
