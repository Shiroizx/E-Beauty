<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SkinType;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display product catalog with filters
     */
    public function index(Request $request)
    {
        $filters = [
            'category_id' => $request->input('category'),
            'brand_ids' => $request->input('brands', []),
            'skin_type_ids' => $request->input('skin_types', []),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'min_rating' => $request->input('min_rating'),
            'in_stock_only' => $request->boolean('in_stock_only'),
            'sort_by' => $request->input('sort_by', 'newest'),
            'per_page' => $request->input('per_page', 12),
        ];

        // Handle search
        if ($request->has('search')) {
            $products = $this->productService->searchProducts($request->input('search'), $filters);
        } else {
            $products = $this->productService->getFilteredProducts($filters);
        }

        // Get filter options
        $brands = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->roots()->with('children')->orderBy('name')->get();
        $skinTypes = SkinType::orderBy('name')->get();

        // Get price range for slider
        $priceRange = \App\Models\Product::active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('catalog.index', compact(
            'products',
            'brands',
            'categories',
            'skinTypes',
            'priceRange',
            'filters'
        ));
    }

    /**
     * Display product detail
     */
    public function show(string $slug)
    {
        $product = $this->productService->getProductDetail($slug);
        $relatedProducts = $this->productService->getRelatedProducts($product->id, 4);

        $inWishlist = auth()->check()
            && WishlistItem::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();

        return view('products.show', compact('product', 'relatedProducts', 'inWishlist'));
    }

    /**
     * Search products (AJAX)
     */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        $filters = ['per_page' => 10];

        $products = $this->productService->searchProducts($keyword, $filters);

        return response()->json([
            'products' => $products->items(),
            'total' => $products->total(),
        ]);
    }

    /**
     * Check product availability (AJAX)
     */
    public function checkAvailability(Request $request)
    {
        $productId = $request->input('product_id');
        $availability = $this->productService->checkAvailability($productId);

        return response()->json($availability);
    }
}
