<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\PromoService;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected PromoService $promoService
    ) {}

    /**
     * Display the homepage
     */
    public function index()
    {
        $featuredProducts = $this->productService->getFeaturedProducts(8);
        $newArrivals = $this->productService->getNewArrivals(8);
        $bestSellers = $this->productService->getBestSellers(8);
        
        $categories = Category::active()
            ->roots()
            ->withCount('products')
            ->limit(6)
            ->get();

        $brands = Brand::active()
            ->withCount('products')
            ->limit(8)
            ->get();

        $homePromos = collect();
        if (auth()->check()) {
            $homePromos = Cache::remember(
                'home_promos_v1',
                now()->addSeconds(90),
                fn () => $this->promoService->getAvailablePromosForHome(10)
            );
        }

        return view('home', compact(
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'categories',
            'brands',
            'homePromos'
        ));
    }
}
