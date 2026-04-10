<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Akun dummy Super Admin (aman dijalankan ulang).
     */
    public function run(): void
    {
        $super = User::firstOrCreate(
            ['email' => 'superadmin@ebeauty.com'],
            [
                'name' => 'Super Admin E-Beauty',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $super->role = 'super_admin';
        $super->save();
    }
}
