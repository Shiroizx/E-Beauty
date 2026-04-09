<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShippingStatusUpdated;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user:id,name,email'])
            ->withCount('items');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        
        $allowedSorts = ['created_at', 'order_number', 'total', 'status', 'payment_status'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $perPage = (int) $request->get('per_page', 15);
        $orders = $query->paginate($perPage)->withQueryString();
        
        $statuses = Order::STATUSES;
        $paymentStatuses = Order::PAYMENT_STATUSES;

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);
        $statuses = Order::STATUSES;
        $paymentStatuses = Order::PAYMENT_STATUSES;
        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address_line' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_province' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'max:20'],
            'shipping_courier' => ['nullable', 'string', 'max:50'],
            'shipping_service' => ['nullable', 'string', 'max:100'],
            'customer_notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $order->status;
            
            $order->update($validated);

            // Send notification if status changed
            if ($oldStatus !== $order->status && $order->user && $order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new ShippingStatusUpdated($order));
                } catch (\Exception $e) {
                    // Log error, don't break the transaction
                    \Log::error('Gagal mengirim email status: ' . $e->getMessage());
                }
            }

            // If we had an audit trail package (like spatie/laravel-activitylog), it would track this.
            // For now, we rely on updated_at.

            DB::commit();
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Data pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui pesanan: ' . $e->getMessage())->withInput();
        }
    }

    public function export(Request $request)
    {
        $orders = Order::with(['items', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'admin-orders-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'No. Pesanan',
            'Customer',
            'Email',
            'Tanggal',
            'Status Pesanan',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Subtotal',
            'Ongkos Kirim',
            'Total',
            'Produk',
        ];

        $rows = [];
        foreach ($orders as $order) {
            $products = $order->items->map(fn($item) => $item->product_name . ' (x' . $item->quantity . ')')->join('; ');
            $rows[] = [
                '="' . $order->order_number . '"', // Force Excel to treat as string
                $order->user->name ?? 'Guest',
                $order->user->email ?? '-',
                $order->created_at->format('d/m/Y H:i'),
                $order->statusLabel(),
                $order->payment_status,
                $order->paymentMethodLabel(),
                $order->subtotal,
                $order->shipping_cost,
                $order->total,
                $products,
            ];
        }

        $output = fopen('php://temp', 'r+');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        // Add separator hint for Excel
        fputs($output, "sep=;\n");
        
        fputcsv($output, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function invoice(Order $order)
    {
        $order->load(['items.product:id,name,slug,image', 'user:id,name,email']);

        $html = view('orders.invoice-pdf', compact('order'))->render();
        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    public function print(Request $request, Order $order)
    {
        $order->load(['items.product:id,name,slug,image', 'user']);
        $format = $request->get('format', 'thermal'); // 'thermal' or 'a4'
        $orders = collect([$order]);

        return view('admin.orders.print', compact('orders', 'format'));
    }

    public function printBulk(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        $format = $request->input('format', 'thermal');
        
        if (empty($orderIds)) {
            return back()->with('error', 'Pilih minimal satu pesanan untuk dicetak.');
        }

        $orders = Order::with(['items.product:id,name,slug,image', 'user'])
            ->whereIn('id', $orderIds)
            ->get();

        return view('admin.orders.print', compact('orders', 'format'));
    }
}
