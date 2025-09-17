<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@hotello.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'address' => '123 Admin Street, Admin City',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@hotello.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+1234567891',
            'address' => '456 Staff Avenue, Staff City',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create agent user
        User::create([
            'name' => 'Agent User',
            'email' => 'agent@hotello.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'phone' => '+1234567892',
            'address' => '789 Agent Boulevard, Agent City',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create customer user
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@hotello.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1234567893',
            'address' => '321 Customer Lane, Customer City',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
