<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(5, min(50, $perPage));
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $search = $request->get('q', '');
        $status = $request->get('status', '');
        $dateFrom = $request->get('from', '');
        $dateTo = $request->get('to', '');

        $allowedSorts = ['created_at', 'order_number', 'total', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $query = Order::forUser($user->id)
            ->withCount('items')
            ->with(['items.product:id,name,slug,image']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%');
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy($sortBy, $sortDir);

        $orders = $query->paginate($perPage)->withQueryString();
        $allStatuses = Order::STATUSES;

        return view('orders.index', compact(
            'orders',
            'allStatuses',
            'sortBy',
            'sortDir',
            'search',
            'status',
            'dateFrom',
            'dateTo',
            'perPage'
        ));
    }

    public function show(Request $request, string $orderNumber)
    {
        $user = Auth::user();
        $order = Order::forUser($user->id)
            ->with(['items.product:id,name,slug,image'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $statuses = Order::STATUSES;
        $paymentStatuses = Order::PAYMENT_STATUSES;
        $trackingData = $order->tracking_data;
        $itemReviewStates = $this->reviewService->getReviewStatesForOrder($order, $user->id);

        return view('orders.show', compact('order', 'statuses', 'paymentStatuses', 'trackingData', 'itemReviewStates'));
    }

    public function invoice(string $orderNumber)
    {
        $user = Auth::user();
        $order = Order::forUser($user->id)
            ->with(['items.product:id,name,slug,image', 'user:id,name,email'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $html = view('orders.invoice-pdf', compact('order'))->render();
        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'csv');

        $orders = Order::forUser($user->id)
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders-' . $user->id . '-' . now()->format('Ymd-His');

        if ($format === 'csv') {
            return $this->exportCsv($orders, $filename . '.csv');
        }

        return $this->exportCsv($orders, $filename . '.csv');
    }

    private function exportCsv($orders, string $filename)
    {
        $headers = [
            'No. Pesanan',
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

    public function poll(Request $request)
    {
        $user = Auth::user();
        $lastEtag = $request->header('If-None-Match', '');
        $lastId = (int) $request->get('last_id', 0);

        $orders = Order::forUser($user->id)
            ->where('id', '>', $lastId)
            ->select('id', 'order_number', 'status', 'payment_status', 'updated_at')
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get();

        $etag = '"' . md5($orders->pluck('updated_at')->implode(',')) . '"';

        if ($etag === $lastEtag) {
            return response()->json(['changed' => false], 304);
        }

        return response()->json([
            'changed' => $orders->isNotEmpty(),
            'orders' => $orders,
            'etag' => $etag,
        ])->header('ETag', $etag);
    }
}
