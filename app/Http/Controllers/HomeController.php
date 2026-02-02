<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SkinType;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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

        return view('home', compact(
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'categories',
            'brands'
        ));
    }
}
