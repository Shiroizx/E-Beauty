<?php

namespace App\Services;

use App\Exceptions\CheckoutException;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected DokuService $dokuService,
    ) {}

    public function calculateShippingRates(string $province, string $city, int $totalItems): array
    {
        // 1. Tentukan Base Rate berdasarkan Provinsi/Kota asal Jakarta Pusat
        $baseRate = $this->getBaseRate($province, $city);

        $options = [];

        // --- REGULER ---
        $options['reguler'] = [
            'name' => 'Reguler (2-4 Hari)',
            'price' => $baseRate,
            'couriers' => [
                ['id' => 'jne_reg', 'name' => 'JNE Reguler'],
                ['id' => 'jnt_ez', 'name' => 'J&T EZ'],
                ['id' => 'sicepat_halu', 'name' => 'SiCepat HALU'],
            ]
        ];

        // --- INSTANT/SAMEDAY ---
        // Hanya untuk area Jabodetabek
        if ($this->isJabodetabek($province, $city)) {
            $options['instant'] = [
                'name' => 'Instant / Sameday (Gojek/Grab)',
                'price' => $baseRate + 15000,
                'couriers' => [
                    ['id' => 'gosend', 'name' => 'GoSend Instant'],
                    ['id' => 'grabexpress', 'name' => 'GrabExpress Sameday'],
                ]
            ];
        }

        // --- KARGO ---
        // Hanya jika item > 10
        if ($totalItems > 10) {
            $options['kargo'] = [
                'name' => 'Kargo (Diatas 10 Item)',
                'price' => max(10000, $baseRate * 0.7), // Lebih murah 30% dari reguler
                'couriers' => [
                    ['id' => 'jne_jtr', 'name' => 'JNE Trucking (JTR)'],
                    ['id' => 'sicepat_gokil', 'name' => 'SiCepat Gokil'],
                ]
            ];
        }

        return $options;
    }

    protected function getBaseRate(string $province, string $city): float
    {
        $prov = strtoupper(trim($province));
        
        if ($this->isJabodetabek($province, $city)) {
            return 10000;
        }

        if (in_array($prov, ['JAWA BARAT', 'BANTEN'])) {
            return 15000;
        }
        
        if (in_array($prov, ['JAWA TENGAH', 'DI YOGYAKARTA'])) {
            return 20000;
        }
        
        if (in_array($prov, ['JAWA TIMUR'])) {
            return 25000;
        }

        if (str_contains($prov, 'SUMATERA') || in_array($prov, ['ACEH', 'RIAU', 'KEPULAUAN RIAU', 'JAMBI', 'BENGKULU', 'LAMPUNG', 'KEPULAUAN BANGKA BELITUNG'])) {
            return 40000;
        }

        if (str_contains($prov, 'KALIMANTAN')) {
            return 45000;
        }

        if (str_contains($prov, 'SULAWESI') || in_array($prov, ['BALI', 'NUSA TENGGARA BARAT', 'NUSA TENGGARA TIMUR'])) {
            return 55000;
        }

        if (str_contains($prov, 'MALUKU') || str_contains($prov, 'PAPUA')) {
            return 80000;
        }

        // Default luar daerah
        return 50000;
    }

    protected function isJabodetabek(string $province, string $city): bool
    {
        $prov = strtoupper(trim($province));
        $cityUpper = strtoupper(trim($city));

        if ($prov === 'DKI JAKARTA') return true;
        
        if (in_array($prov, ['JAWA BARAT', 'BANTEN'])) {
            $jabodetabekCities = ['BOGOR', 'DEPOK', 'TANGERANG', 'BEKASI'];
            foreach ($jabodetabekCities as $jb) {
                if (str_contains($cityUpper, $jb)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function validateShippingSelection(string $service, string $courier, string $province, string $city, int $totalItems): ?float
    {
        $rates = $this->calculateShippingRates($province, $city, $totalItems);

        if (!isset($rates[$service])) {
            return null; // Invalid service
        }

        $validCourier = false;
        foreach ($rates[$service]['couriers'] as $c) {
            if ($c['id'] === $courier) {
                $validCourier = true;
                break;
            }
        }

        if (!$validCourier) {
            return null; // Invalid courier
        }

        return $rates[$service]['price'];
    }

    /**
     * @param  array<string, mixed>  $shipping
     * @return array{order: Order, lines: \Illuminate\Support\Collection}
     */
    public function placeOrder(User $user, array $shipping, string $paymentMethod): array
    {
        if (! in_array($paymentMethod, ['bank_transfer', 'cod', 'simulated_card', 'doku'], true)) {
            throw new CheckoutException('Metode pembayaran tidak valid.');
        }

        return DB::transaction(function () use ($user, $shipping, $paymentMethod) {
            $rows = CartItem::query()
                ->where('user_id', $user->id)
                ->with(['product.stock', 'product.brand'])
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) {
                throw new CheckoutException('Keranjang kosong.');
            }

            $productIds = $rows->pluck('product_id')->unique()->sort()->values()->all();
            $stocks = Stock::query()
                ->whereIn('product_id', $productIds)
                ->orderBy('product_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $subtotal = 0.0;
            $prepared = [];

            foreach ($rows as $row) {
                $product = $row->product;
                if (! $product || ! $product->is_active) {
                    throw new CheckoutException('Beberapa produk tidak lagi tersedia. Perbarui keranjang Anda.');
                }

                $stock = $stocks->get($product->id);
                if (! $stock || $stock->quantity < $row->quantity) {
                    throw new CheckoutException('Stok tidak cukup untuk: '.$product->name);
                }

                $unit = (float) $product->final_price;
                $qty = (int) $row->quantity;
                $lineTotal = $unit * $qty;
                $subtotal += $lineTotal;

                $prepared[] = [
                    'product' => $product,
                    'stock' => $stock,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $lineTotal,
                ];
            }

            $totalItems = $rows->sum('quantity');

            $shippingCost = $this->validateShippingSelection(
                $shipping['shipping_service'],
                $shipping['shipping_courier'],
                $shipping['shipping_province'],
                $shipping['shipping_city'],
                $totalItems
            );

            if ($shippingCost === null) {
                throw new CheckoutException('Pilihan pengiriman tidak valid untuk area atau jumlah barang Anda.');
            }

            // Optional: apply free shipping if subtotal > config
            $freeAt = (float) config('checkout.free_shipping_subtotal', 500_000);
            if ($subtotal >= $freeAt) {
                $shippingCost = 0.0;
            }

            $total = $subtotal + $shippingCost;

            [$status, $paymentStatus] = $this->resolvePaymentState($paymentMethod);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_name' => $shipping['shipping_name'],
                'shipping_phone' => $shipping['shipping_phone'],
                'shipping_address_line' => $shipping['shipping_address_line'],
                'shipping_city' => $shipping['shipping_city'],
                'shipping_province' => $shipping['shipping_province'],
                'shipping_district' => $shipping['shipping_district'] ?? null,
                'shipping_subdistrict' => $shipping['shipping_subdistrict'] ?? null,
                'shipping_postal_code' => $shipping['shipping_postal_code'],
                'customer_notes' => $shipping['customer_notes'] ?? null,
                'shipping_courier' => $shipping['shipping_courier'],
                'shipping_service' => $shipping['shipping_service'],
            ]);

            foreach ($prepared as $line) {
                $product = $line['product'];
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $line['stock']->reduce($line['quantity']);
            }

            $this->cartService->clearForUser($user->id);

            $order = $order->fresh(['items']);

            // If DOKU payment, create checkout session
            if ($paymentMethod === 'doku') {
                try {
                    $dokuResult = $this->dokuService->createCheckout([
                        'invoice_number' => $order->order_number,
                        'amount'         => (int) $total,
                        'customer_name'  => $shipping['shipping_name'],
                        'customer_email' => $user->email,
                    ]);

                    $order->update([
                        'doku_invoice_id'    => $order->order_number,
                        'doku_request_id'    => $dokuResult['request_id'],
                        'doku_payment_url'   => $dokuResult['payment_url'],
                        'payment_expired_at' => Carbon::now()->addMinutes((int) config('doku.payment_due_minutes', 60)),
                    ]);
                } catch (\Throwable $e) {
                    // Order was created but DOKU session failed — update status
                    $order->update([
                        'status'         => 'pending_payment',
                        'payment_status' => 'failed',
                    ]);
                    throw new CheckoutException('Pesanan dibuat tetapi gagal membuat sesi DOKU: ' . $e->getMessage());
                }
            }

            return [
                'order' => $order,
                'lines' => collect($prepared),
            ];
        });
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function resolvePaymentState(string $paymentMethod): array
    {
        return match ($paymentMethod) {
            'simulated_card' => ['processing', 'paid'],
            'cod' => ['confirmed', 'pending'],
            'bank_transfer' => ['pending_payment', 'pending'],
            'doku' => ['pending_payment', 'pending'],
            default => ['pending_payment', 'pending'],
        };
    }

    protected function generateOrderNumber(): string
    {
        do {
            $n = 'EB-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('order_number', $n)->exists());

        return $n;
    }
}
