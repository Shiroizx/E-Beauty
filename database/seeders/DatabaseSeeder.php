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
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            SkinTypeSeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
            PromoSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
