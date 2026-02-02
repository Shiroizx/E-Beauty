<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SkinType;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::all();
        $categories = Category::whereNotNull('parent_id')->get(); // Only subcategories
        $skinTypes = SkinType::all();

        $products = [
            ['name' => 'Niacinamide 10% + Zinc 1%', 'brand' => 'The Ordinary', 'category' => 'Serum', 'price' => 95000, 'skin_types' => ['Oily', 'Combination']],
            ['name' => 'Hydrating Cleanser', 'brand' => 'CeraVe', 'category' => 'Cleanser', 'price' => 185000, 'skin_types' => ['Normal', 'Dry', 'Sensitive']],
            ['name' => 'Effaclar Duo (+)', 'brand' => 'La Roche-Posay', 'category' => 'Acne Treatment', 'price' => 276000, 'discount' => 249000, 'skin_types' => ['Oily']],
            ['name' => 'Hydro Boost Water Gel', 'brand' => 'Neutrogena', 'category' => 'Moisturizer', 'price' => 195000, 'skin_types' => ['Normal', 'Combination']],
            ['name' => 'Daily Facial Cleanser', 'brand' => 'Cetaphil', 'category' => 'Cleanser', 'price' => 110000, 'skin_types' => ['Sensitive']],
            ['name' => 'Green Tea Fresh Toner', 'brand' => 'Innisfree', 'category' => 'Toner', 'price' => 150000, 'skin_types' => ['Normal', 'Oily']],
            ['name' => 'Advanced Snail 96 Mucin Power Essence', 'brand' => 'COSRX', 'category' => 'Serum', 'price' => 210000, 'skin_types' => ['Dry', 'Normal']],
            ['name' => 'AHA BHA PHA 30 Days Miracle Toner', 'brand' => 'Some By Mi', 'category' => 'Toner', 'price' => 175000, 'skin_types' => ['Oily', 'Combination']],
            ['name' => '5X Ceramide Barrier Repair Moisture Gel', 'brand' => 'Skintific', 'category' => 'Moisturizer', 'price' => 89000, 'skin_types' => ['Sensitive', 'Dry']],
            ['name' => 'Acne Gentle Cleansing Foam', 'brand' => 'Azarine', 'category' => 'Cleanser', 'price' => 35000, 'skin_types' => ['Oily']],
            ['name' => 'Lightening Face Toner', 'brand' => 'Wardah', 'category' => 'Toner', 'price' => 25000, 'skin_types' => ['Normal']],
            ['name' => 'Bright Stuff Face Wash', 'brand' => 'Emina', 'category' => 'Cleanser', 'price' => 18000, 'skin_types' => ['Normal', 'Oily']],
            ['name' => 'Hyaluronic Acid + B5', 'brand' => 'Somethinc', 'category' => 'Serum', 'price' => 159000, 'skin_types' => ['Dry', 'Normal']],
            ['name' => 'Your Skin Bae Brightening Face Wash', 'brand' => 'Avoskin', 'category' => 'Cleanser', 'price' => 89000, 'skin_types' => ['Normal']],
            ['name' => 'Ultra Protecting Sunscreen SPF 50', 'brand' => 'Wardah', 'category' => 'Sunscreen', 'price' => 38000, 'skin_types' => ['Normal', 'Oily', 'Combination']],
        ];

        foreach ($products as $productData) {
            $brand = $brands->where('name', $productData['brand'])->first();
            $category = $categories->where('name', $productData['category'])->first();
            
            if (!$brand || !$category) continue;

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => "Produk {$productData['name']} dari {$productData['brand']} adalah solusi terbaik untuk perawatan kulit Anda. Diformulasikan dengan bahan-bahan berkualitas tinggi yang aman dan efektif.",
                'ingredients' => 'Aqua, Glycerin, Niacinamide, Butylene Glycol, dan bahan-bahan aktif lainnya',
                'how_to_use' => 'Gunakan pagi dan malam hari setelah membersihkan wajah. Aplikasikan secukupnya pada wajah dan leher.',
                'price' => $productData['price'],
                'discount_price' => $productData['discount'] ?? null,
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'weight' => rand(50, 200),
                'size' => rand(30, 100) . 'ml',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'is_active' => true,
                'is_featured' => rand(0, 1) == 1,
            ]);

            // Attach skin types
            $skinTypeIds = $skinTypes->whereIn('name', $productData['skin_types'])->pluck('id')->toArray();
            $product->skinTypes()->attach($skinTypeIds);
        }
    }
}
