<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Get filtered products based on various criteria
     */
    public function getFilteredProducts(array $filters = [])
    {
        $query = Product::query()
            ->with(['brand', 'category', 'stock', 'skinTypes'])
            ->active();

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->inCategory($filters['category_id']);
        }

        // Filter by brand(s)
        if (!empty($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }

        // Filter by skin type(s)
        if (!empty($filters['skin_type_ids'])) {
            $query->whereHas('skinTypes', function ($q) use ($filters) {
                $q->whereIn('skin_types.id', $filters['skin_type_ids']);
            });
        }

        // Filter by price range
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $min = $filters['min_price'] ?? 0;
            $max = $filters['max_price'] ?? PHP_INT_MAX;
            $query->priceRange($min, $max);
        }

        // Filter by minimum rating
        if (!empty($filters['min_rating'])) {
            $query->whereHas('reviews', function ($q) use ($filters) {
                $q->approved();
            })->withAvg('reviews as avg_rating', 'rating')
              ->having('avg_rating', '>=', $filters['min_rating']);
        }

        // Filter by stock availability
        if (!empty($filters['in_stock_only'])) {
            $query->inStock();
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'newest';
        
        switch ($sortBy) {
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews as avg_rating', 'rating')
                      ->orderByDesc('avg_rating');
                break;
            case 'popularity':
                $query->withCount('reviews')
                      ->orderByDesc('reviews_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Search products by keyword
     */
    public function searchProducts(string $keyword, array $filters = [])
    {
        $filters['keyword'] = $keyword;

        $query = Product::query()
            ->with(['brand', 'category', 'stock'])
            ->active();

        // Search in name, description, and ingredients
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('ingredients', 'like', "%{$keyword}%")
              ->orWhereHas('brand', function ($brandQuery) use ($keyword) {
                  $brandQuery->where('name', 'like', "%{$keyword}%");
              })
              ->orWhereHas('category', function ($catQuery) use ($keyword) {
                  $catQuery->where('name', 'like', "%{$keyword}%");
              });
        });

        return $this->getFilteredProducts($filters);
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 8)
    {
        return Product::with(['brand', 'category', 'stock'])
            ->active()
            ->featured()
            ->inStock()
            ->limit($limit)
            ->get();
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals(int $limit = 8)
    {
        return Product::with(['brand', 'category', 'stock'])
            ->active()
            ->inStock()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get best sellers based on review count
     */
    public function getBestSellers(int $limit = 8)
    {
        return Product::with(['brand', 'category', 'stock'])
            ->active()
            ->inStock()
            ->withCount('reviews')
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product detail with all relations
     */
    public function getProductDetail(string $slug)
    {
        return Product::with([
            'brand',
            'category',
            'stock',
            'skinTypes',
            'reviews' => function ($query) {
                $query->approved()
                      ->with('user')
                      ->orderByDesc('created_at');
            }
        ])->where('slug', $slug)
          ->firstOrFail();
    }

    /**
     * Get related products (same category or brand)
     */
    public function getRelatedProducts(int $productId, int $limit = 4)
    {
        $product = Product::findOrFail($productId);

        return Product::with(['brand', 'category', 'stock'])
            ->active()
            ->inStock()
            ->where('id', '!=', $productId)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Check product availability
     */
    public function checkAvailability(int $productId)
    {
        $product = Product::with('stock')->findOrFail($productId);

        return [
            'available' => $product->is_in_stock,
            'quantity' => $product->stock_quantity,
            'is_low_stock' => $product->stock && $product->stock->is_low_stock,
        ];
    }

    /**
     * Get products for a specific brand
     */
    public function getProductsByBrand(int $brandId, array $filters = [])
    {
        $filters['brand_ids'] = [$brandId];
        return $this->getFilteredProducts($filters);
    }

    /**
     * Get products for a specific category
     */
    public function getProductsByCategory(int $categoryId, array $filters = [])
    {
        $filters['category_id'] = $categoryId;
        return $this->getFilteredProducts($filters);
    }
}
