<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UUser;
use Illuminate\Support\Facades\Hash;

class UUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        UUser::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'activate' => true
        ]);

        // Create sample student
        UUser::create([
            'firstname' => 'Student',
            'lastname' => 'User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'activate' => true
        ]);

        // Create additional sample users using factory
        UUser::factory()->count(8)->create();
    }
}