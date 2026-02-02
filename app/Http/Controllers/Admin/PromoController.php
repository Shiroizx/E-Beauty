<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Product;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Display promo list
     */
    public function index()
    {
        $promos = Promo::latest()->paginate(20);

        return view('admin.promos.index', compact('promos'));
    }

    /**
     * Show create promo form
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();

        return view('admin.promos.create', compact('products'));
    }

    /**
     * Store new promo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promos,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'is_active' => 'boolean',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);
        $validated['used_count'] = 0;

        $promo = Promo::create($validated);

        // Attach products if product-specific promo
        if (!empty($validated['product_ids'])) {
            $promo->products()->attach($validated['product_ids']);
        }

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan');
    }

    /**
     * Show edit promo form
     */
    public function edit(Promo $promo)
    {
        $promo->load('products');
        $products = Product::active()->orderBy('name')->get();

        return view('admin.promos.edit', compact('promo', 'products'));
    }

    /**
     * Update promo
     */
    public function update(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promos,code,' . $promo->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $promo->update($validated);

        // Sync products
        if (isset($validated['product_ids'])) {
            $promo->products()->sync($validated['product_ids']);
        } else {
            $promo->products()->detach();
        }

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diupdate');
    }

    /**
     * Delete promo
     */
    public function destroy(Promo $promo)
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus');
    }
}
