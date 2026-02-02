<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use App\Models\Product;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        Promo::create([
            'name' => 'Diskon 20% All Products',
            'code' => 'BEAUTY20',
            'description' => 'Diskon 20% untuk semua produk',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_purchase' => 50000,
            'max_discount' => 75000,
            'usage_limit' => 100,
            'usage_per_user' => 1,
            'used_count' => 0,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
            'is_active' => true,
        ]);

        Promo::create([
            'name' => 'Potongan Rp 50.000',
            'code' => 'HEMAT50',
            'description' => 'Potongan langsung Rp 50.000 untuk pembelian min. Rp 200.000',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_purchase' => 200000,
            'usage_limit' => 50,
            'usage_per_user' => 1,
            'used_count' => 0,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addWeeks(2),
            'is_active' => true,
        ]);
    }
}
