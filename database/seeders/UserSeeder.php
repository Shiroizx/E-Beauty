<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin E-Beauty',
            'email' => 'admin@ebeauty.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create regular users
        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jessica Lee',
            'email' => 'jessica@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
