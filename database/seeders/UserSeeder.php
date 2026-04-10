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
        // Create admin user (role diset di luar mass assignment agar tidak bisa diubah lewat request biasa)
        $admin = User::create([
            'name' => 'Admin E-Beauty',
            'email' => 'admin@ebeauty.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->role = 'admin';
        $admin->save();

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
