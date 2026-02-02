<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SkinType;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display product list
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'stock']);

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        // Filter by brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->latest()->paginate(20);
        $brands = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->roots()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();
        $skinTypes = SkinType::orderBy('name')->get();

        return view('admin.products.create', compact('brands', 'categories', 'skinTypes'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|unique:products,sku',
            'weight' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:50',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'skin_type_ids' => 'nullable|array',
            'skin_type_ids.*' => 'exists:skin_types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'warehouse_location' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle main image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $product = Product::create($validated);

        // Attach skin types
        if (!empty($validated['skin_type_ids'])) {
            $product->skinTypes()->attach($validated['skin_type_ids']);
        }

        // Create stock record
        Stock::create([
            'product_id' => $product->id,
            'quantity' => $request->input('stock_quantity', 0),
            'min_quantity' => $request->input('min_quantity', 5),
            'warehouse_location' => $request->input('warehouse_location'),
            'batch_number' => $request->input('batch_number'),
            'expiry_date' => $request->input('expiry_date'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Show edit product form
     */
    public function edit(Product $product)
    {
        $product->load(['brand', 'category', 'skinTypes', 'stock']);
        $brands = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();
        $skinTypes = SkinType::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories', 'skinTypes'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'weight' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:50',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'skin_type_ids' => 'nullable|array',
            'skin_type_ids.*' => 'exists:skin_types,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Update slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery upload
        if ($request->hasFile('gallery')) {
            // Delete old gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $product->update($validated);

        // Sync skin types
        if (isset($validated['skin_type_ids'])) {
            $product->skinTypes()->sync($validated['skin_type_ids']);
        } else {
            $product->skinTypes()->detach();
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        // Delete images
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->gallery) {
            foreach ($product->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return back()->with('success', 'Status produk berhasil diupdate');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Product $product)
    {
        $product->is_featured = !$product->is_featured;
        $product->save();

        return back()->with('success', 'Status featured berhasil diupdate');
    }
}
