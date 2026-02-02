<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\StockService;
use App\Services\ReviewService;
use App\Services\PromoService;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $productService;
    protected $stockService;
    protected $reviewService;
    protected $promoService;

    public function __construct(
        ProductService $productService,
        StockService $stockService,
        ReviewService $reviewService,
        PromoService $promoService
    ) {
        $this->productService = $productService;
        $this->stockService = $stockService;
        $this->reviewService = $reviewService;
        $this->promoService = $promoService;
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Statistics
        $stats = [
            'total_products' => Product::active()->count(),
            'total_brands' => Brand::active()->count(),
            'total_categories' => Category::active()->count(),
            'stock_stats' => $this->stockService->getStatistics(),
            'review_stats' => $this->reviewService->getStatistics(),
            'promo_stats' => $this->promoService->getStatistics(),
        ];

        // Low stock products
        $lowStockProducts = $this->stockService->getLowStockProducts()->take(10);

        // Recent reviews pending approval
        $pendingReviews = $this->reviewService->getPendingReviews();

        // Top products by review count
        $topProducts = Product::active()
            ->withCount('reviews')
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        // Products by category (for chart)
        $categoryStats = Category::active()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'lowStockProducts',
            'pendingReviews',
            'topProducts',
            'categoryStats'
        ));
    }
}
