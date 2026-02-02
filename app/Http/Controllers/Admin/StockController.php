<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display stock management page
     */
    public function index(Request $request)
    {
        $query = Stock::with('product');

        // Filter
        $filter = $request->input('filter', 'all');
        if ($filter === 'low_stock') {
            $query->lowStock();
        } elseif ($filter === 'expiring') {
            $query->expiringSoon(30);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $stocks = $query->paginate(20);
        $statistics = $this->stockService->getStatistics();

        return view('admin.stocks.index', compact('stocks', 'filter', 'statistics'));
    }

    /**
     * Update stock
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'warehouse_location' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $this->stockService->updateStock(
            $product->id,
            $validated['quantity'],
            'set',
            [
                'min_quantity' => $validated['min_quantity'] ?? 5,
                'warehouse_location' => $validated['warehouse_location'],
                'batch_number' => $validated['batch_number'],
                'expiry_date' => $validated['expiry_date'],
            ]
        );

        return back()->with('success', 'Stok berhasil diupdate');
    }

    /**
     * Display low stock products
     */
    public function lowStock()
    {
        $lowStockProducts = $this->stockService->getLowStockProducts();

        return view('admin.stocks.low-stock', compact('lowStockProducts'));
    }
}
