<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'The Ordinary',
            'CeraVe',
            'La Roche-Posay',
            'Neutrogena',
            'Cetaphil',
            'Innisfree',
            'COSRX',
            'Some By Mi',
            'Skintific',
            'Azarine',
            'Wardah',
            'Emina',
            'Somethinc',
            'Avoskin',
            'Luxcrime',
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'description' => "Brand kecantikan premium dengan produk berkualitas tinggi untuk perawatan kulit.",
                'is_active' => true,
            ]);
        }
    }
}
