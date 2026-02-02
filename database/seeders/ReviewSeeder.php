<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::take(5)->get();
        $users = User::where('email', '!=', 'admin@ebeauty.com')->get();

        $comments = [
            'Produk yang sangat bagus! Hasilnya terlihat setelah pemakaian rutin.',
            'Cocok untuk kulit saya, tidak menyebabkan iritasi.',
            'Harga terjangkau dengan kualitas yang baik.',
            'Teksturnya ringan dan mudah meresap, saya suka!',
            'Packaging nya bagus dan produknya original.',
        ];

        foreach ($products as $product) {
            // Get random users for this product, ensuring uniqueness
            $productReviewers = $users->random(rand(2, min(4, $users->count())));
            
            foreach ($productReviewers as $user) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => rand(4, 5),
                    'comment' => $comments[array_rand($comments)],
                    'is_verified_purchase' => rand(0, 1) == 1,
                    'is_approved' => true,
                ]);
            }
        }
    }
}
