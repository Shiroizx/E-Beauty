<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:50'
        ]);

        return redirect()->route('track.show', $request->order_number);
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('track.index')->with('error', 'Nomor resi/pesanan tidak ditemukan.');
        }

        // Mocking tracking data based on order status
        $trackingData = $order->tracking_data;

        return view('tracking.show', compact('order', 'trackingData'));
    }
}