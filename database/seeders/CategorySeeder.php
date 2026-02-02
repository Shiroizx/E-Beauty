<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parent categories
        $skincare = Category::create([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'description' => 'Produk perawatan kulit wajah dan tubuh',
            'is_active' => true,
        ]);

        $makeup = Category::create([
            'name' => 'Makeup',
            'slug' => 'makeup',
            'description' => 'Produk makeup untuk mempercantik penampilan',
            'is_active' => true,
        ]);

$haircare = Category::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'description' => 'Produk perawatan rambut',
            'is_active' => true,
        ]);

        $bodycare = Category::create([
            'name' => 'Body Care',
            'slug' => 'body-care',
            'description' => 'Produk perawatan tubuh',
            'is_active' => true,
        ]);

        // Skincare subcategories
        $skincareSubcategories = [
            'Cleanser' => 'Pembersih wajah',
            'Toner' => 'Penyegar wajah',
            'Serum' => 'Serum perawatan intensif',
            'Moisturizer' => 'Pelembab wajah',
            'Sunscreen' => 'Tabir surya',
            'Face Mask' => 'Masker wajah',
            'Eye Cream' => 'Krim mata',
            'Acne Treatment' => 'Perawatan jerawat',
        ];

        foreach ($skincareSubcategories as $name => $desc) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $desc,
                'parent_id' => $skincare->id,
                'is_active' => true,
            ]);
        }

        // Makeup subcategories
        $makeupSubcategories = [
            'Foundation' => 'Alas bedak',
            'Concealer' => 'Penutup noda',
            'Powder' => 'Bedak',
            'Blush' => 'Perona pipi',
            'Lipstick' => 'Lipstik',
            'Eyeshadow' => 'Eyeshadow',
            'Mascara' => 'Maskara',
            'Eyeliner' => 'Eyeliner',
        ];

        foreach ($makeupSubcategories as $name => $desc) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $desc,
                'parent_id' => $makeup->id,
                'is_active' => true,
            ]);
        }
    }
}
