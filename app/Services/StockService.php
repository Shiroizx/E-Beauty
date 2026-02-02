<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Product;
use Carbon\Carbon;

class StockService
{
    /**
     * Get products with low stock
     */
    public function getLowStockProducts()
    {
        return Stock::with('product')
            ->lowStock()
            ->get()
            ->map(function ($stock) {
                return [
                    'product' => $stock->product,
                    'current_quantity' => $stock->quantity,
                    'min_quantity' => $stock->min_quantity,
                    'difference' => $stock->min_quantity - $stock->quantity,
                ];
            });
    }

    /**
     * Get products expiring soon
     */
    public function getExpiringSoonProducts(int $days = 30)
    {
        return Stock::with('product')
            ->expiringSoon($days)
            ->get()
            ->map(function ($stock) {
                return [
                    'product' => $stock->product,
                    'expiry_date' => $stock->expiry_date,
                    'days_until_expiry' => Carbon::now()->diffInDays($stock->expiry_date),
                    'batch_number' => $stock->batch_number,
                ];
            });
    }

    /**
     * Update stock for a product
     */
    public function updateStock(int $productId, int $quantity, string $type = 'set', array $additionalData = [])
    {
        $stock = Stock::firstOrCreate(
            ['product_id' => $productId],
            [
                'quantity' => 0,
                'min_quantity' => 5,
            ]
        );

        switch ($type) {
            case 'add':
                $stock->add($quantity);
                break;
            case 'reduce':
                if ($stock->quantity < $quantity) {
                    throw new \Exception('Insufficient stock');
                }
                $stock->reduce($quantity);
                break;
            case 'set':
            default:
                $stock->quantity = $quantity;
                break;
        }

        // Update additional fields if provided
        if (!empty($additionalData)) {
            $stock->fill($additionalData);
        }

        $stock->save();

        return $stock;
    }

    /**
     * Bulk update stock
     */
    public function bulkUpdateStock(array $stockData)
    {
        $results = [];

        foreach ($stockData as $data) {
            try {
                $results[] = $this->updateStock(
                    $data['product_id'],
                    $data['quantity'],
                    $data['type'] ?? 'set',
                    $data['additional_data'] ?? []
                );
            } catch (\Exception $e) {
                $results[] = [
                    'error' => $e->getMessage(),
                    'product_id' => $data['product_id'],
                ];
            }
        }

        return $results;
    }

    /**
     * Get stock statistics
     */
    public function getStatistics()
    {
        return [
            'total_items' => Stock::count(),
            'total_quantity' => (int) Stock::sum('quantity'),
            'total_products' => Product::active()->count(),
            'in_stock_count' => Product::active()->inStock()->count(),
            'out_of_stock_count' => Product::active()->count() - Product::active()->inStock()->count(),
            'low_stock_count' => Stock::lowStock()->count(),
            'expiring_soon_count' => Stock::expiringSoon(30)->count(),
        ];
    }

    /**
     * Check if product has sufficient stock
     */
    public function hasSufficientStock(int $productId, int $requiredQuantity)
    {
        $stock = Stock::where('product_id', $productId)->first();

        if (!$stock) {
            return false;
        }

        return $stock->quantity >= $requiredQuantity;
    }

    /**
     * Reserve stock (for orders)
     */
    public function reserveStock(int $productId, int $quantity)
    {
        if (!$this->hasSufficientStock($productId, $quantity)) {
            throw new \Exception('Insufficient stock for product ID: ' . $productId);
        }

        return $this->updateStock($productId, $quantity, 'reduce');
    }

    /**
     * Release stock (cancel order)
     */
    public function releaseStock(int $productId, int $quantity)
    {
        return $this->updateStock($productId, $quantity, 'add');
    }
}
