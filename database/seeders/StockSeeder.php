<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => rand(10, 150),
                'min_quantity' => 10,
                'warehouse_location' => 'Gudang ' . chr(rand(65, 68)), // A-D
                'batch_number' => 'BATCH-' . date('Ymd') . '-' . str_pad($product->id, 3, '0', STR_PAD_LEFT),
                'expiry_date' => Carbon::now()->addMonths(rand(12, 36)),
            ]);
        }
    }
}
