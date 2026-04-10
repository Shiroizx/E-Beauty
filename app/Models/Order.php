<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending_payment' => 'Menunggu pembayaran',
        'processing' => 'Diproses',
        'confirmed' => 'Dikonfirmasi',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const PAYMENT_STATUSES = [
        'pending' => 'Menunggu',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'promo_code',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'total',
        'shipping_name',
        'shipping_phone',
        'shipping_address_line',
        'shipping_city',
        'shipping_province',
        'shipping_district',
        'shipping_subdistrict',
        'shipping_postal_code',
        'customer_notes',
        'shipping_courier',
        'shipping_service',
        'doku_invoice_id',
        'doku_request_id',
        'doku_payment_url',
        'payment_expired_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_expired_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTrackingDataAttribute()
    {
        $events = [];
        $createdAt = $this->created_at;

        // Base location for shop
        $shopLat = -6.200000;
        $shopLng = 106.816666; // Jakarta

        // Destination location (Mocked randomly or based on city)
        $destLat = -6.914744; // Bandung
        $destLng = 107.609810;

        // 1. Pending Payment
        $events[] = [
            'time' => $createdAt->format('Y-m-d H:i:s'),
            'status' => 'Pesanan Dibuat',
            'description' => 'Menunggu pembayaran dari pelanggan.',
            'location' => 'Sistem Skinbae.ID',
            'lat' => null,
            'lng' => null,
            'completed' => true
        ];

        if ($this->status !== 'pending_payment') {
            // 2. Processing
            $processTime = $createdAt->copy()->addMinutes(15);
            $events[] = [
                'time' => $processTime->format('Y-m-d H:i:s'),
                'status' => 'Pesanan Diproses',
                'description' => 'Pembayaran diterima. Pesanan sedang disiapkan.',
                'location' => 'Gudang Skinbae.ID',
                'lat' => $shopLat,
                'lng' => $shopLng,
                'completed' => true
            ];
        }

        if (in_array($this->status, ['shipped', 'completed'])) {
            // 3. Shipped / Picked up
            $shippedTime = $createdAt->copy()->addHours(5);
            $events[] = [
                'time' => $shippedTime->format('Y-m-d H:i:s'),
                'status' => 'Diserahkan ke Kurir',
                'description' => 'Paket telah diserahkan ke kurir ' . strtoupper($this->shipping_courier ?? 'REG'),
                'location' => 'Drop Point Jakarta',
                'lat' => -6.21462,
                'lng' => 106.84513,
                'completed' => true
            ];

            // 4. In Transit
            $transitTime = $createdAt->copy()->addHours(12);
            $events[] = [
                'time' => $transitTime->format('Y-m-d H:i:s'),
                'status' => 'Dalam Perjalanan',
                'description' => 'Paket sedang dibawa menuju kota tujuan.',
                'location' => 'Transit Hub',
                'lat' => -6.5971, // Bogor
                'lng' => 106.7932,
                'completed' => true
            ];
        }

        if ($this->status === 'completed') {
            // 5. Delivered
            $deliveredTime = $createdAt->copy()->addDays(2);
            $events[] = [
                'time' => $deliveredTime->format('Y-m-d H:i:s'),
                'status' => 'Pesanan Selesai',
                'description' => 'Paket telah diterima oleh: ' . $this->shipping_name,
                'location' => $this->shipping_city,
                'lat' => $destLat,
                'lng' => $destLng,
                'completed' => true
            ];
        } elseif ($this->status === 'shipped') {
            // Add a pending delivery event
            $events[] = [
                'time' => null,
                'status' => 'Estimasi Tiba',
                'description' => 'Paket akan segera tiba di alamat tujuan.',
                'location' => $this->shipping_city,
                'lat' => $destLat,
                'lng' => $destLng,
                'completed' => false
            ];
        }

        // Sort desc so newest is on top
        return collect($events)->sortByDesc('time')->values()->toArray();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return $this->formatMoney($this->subtotal);
    }

    public function getFormattedShippingAttribute(): string
    {
        return $this->formatMoney($this->shipping_cost);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return $this->formatMoney($this->discount_amount);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->formatMoney($this->total);
    }

    protected function formatMoney($value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }

    public function paymentMethodLabel(): string
    {
        return match (strtolower($this->payment_method)) {
            'bank_transfer' => 'Transfer Bank',
            'cod' => 'Bayar di Tempat (COD)',
            'simulated_card' => 'Kartu (demo / simulasi)',
            'doku' => 'DOKU Payment Gateway',
            default => strtoupper(str_replace('_', ' ', $this->payment_method)),
        };
    }

    public function isDokuPayment(): bool
    {
        return $this->payment_method === 'doku' || !empty($this->doku_invoice_id);
    }

    public function isDokuExpired(): bool
    {
        return $this->isDokuPayment()
            && $this->payment_expired_at
            && $this->payment_expired_at->isPast();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_payment' => 'Menunggu pembayaran',
            'processing' => 'Diproses',
            'confirmed' => 'Dikonfirmasi',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }
}