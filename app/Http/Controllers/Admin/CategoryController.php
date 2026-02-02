<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display category list
     */
    public function index()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->latest()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create category form
     */
    public function create()
    {
        $parentCategories = Category::roots()->active()->orderBy('name')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Show edit category form
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::roots()
            ->active()
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $category->id) {
            return back()->with('error', 'Kategori tidak bisa menjadi parent dari dirinya sendiri');
        }

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki sub-kategori');
        }

        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
