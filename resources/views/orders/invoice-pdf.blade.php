<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 13px; color: #1a1a2e; line-height: 1.5; }
        .invoice-wrap { max-width: 760px; margin: 0 auto; padding: 40px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #f4518c; padding-bottom: 24px; }
        .brand-name { font-size: 28px; font-weight: 800; color: #b82d5c; letter-spacing: -0.5px; }
        .brand-tagline { font-size: 11px; color: #737380; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-title { font-size: 22px; font-weight: 700; color: #1a1a2e; }
        .invoice-number { font-size: 14px; color: #f4518c; font-weight: 700; margin-top: 4px; letter-spacing: 0.05em; }
        .invoice-date { font-size: 12px; color: #737380; margin-top: 2px; }
        .two-col { display: flex; gap: 40px; margin-bottom: 32px; }
        .col h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 0.12em; color: #b82d5c; margin-bottom: 8px; font-weight: 700; }
        .col p { font-size: 13px; color: #3a3a4a; line-height: 1.7; }
        .col p strong { color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        thead tr { background: #faf5f7; }
        thead th { padding: 12px 16px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #b82d5c; font-weight: 700; border-bottom: 2px solid #ffd0e5; }
        thead th:last-child, tbody td:last-child { text-align: right; }
        thead th:nth-child(2) { text-align: center; }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #f0e8ef; font-size: 13px; color: #3a3a4a; vertical-align: top; }
        tbody tr:last-child td { border-bottom: none; }
        .item-name { font-weight: 600; color: #1a1a2e; }
        .item-sku { font-size: 11px; color: #a1a1aa; margin-top: 2px; }
        .text-center { text-align: center; }
        .totals { margin-left: auto; max-width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f0e8ef; font-size: 13px; color: #3a3a4a; }
        .totals-row:last-child { border-bottom: none; }
        .totals-row.grand { padding-top: 10px; border-top: 2px solid #f4518c; font-size: 16px; font-weight: 800; color: #b82d5c; }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid #f0e8ef; text-align: center; font-size: 11px; color: #a1a1aa; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .status-paid { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    </style>
</head>
<body>
<div class="invoice-wrap">
    <div class="invoice-header">
        <div>
            <div class="brand-name">Skinbae.ID</div>
            <div class="brand-tagline">Premium Beauty & Skincare</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $order->order_number }}</div>
            <div class="invoice-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
            <div style="margin-top:6px;">
                <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}
                </span>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div class="col">
            <h3>Dikirim ke</h3>
            <p>
                <strong>{{ $order->shipping_name }}</strong><br>
                {{ $order->shipping_phone }}<br>
                {{ $order->shipping_address_line }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
            </p>
        </div>
        <div class="col">
            <h3>Metode Pembayaran</h3>
            <p>
                <strong>{{ $order->paymentMethodLabel() }}</strong><br>
                Status: {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}<br>
                @if($order->payment_method === 'doku' && $order->doku_payment_url && $order->payment_status !== 'paid')
                    Bayar di: {{ $order->doku_payment_url }}<br>
                @endif
                @if($order->payment_expired_at && $order->payment_status !== 'paid')
                    Batas: {{ $order->payment_expired_at->format('d M Y, H:i') }} WIB
                @endif
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->product_sku)
                            <div class="item-sku">SKU: {{ $item->product_sku }}</div>
                        @endif
                        <div style="font-size:12px;color:#737380;margin-top:2px;">{{ $item->formatted_unit_price }} / item</div>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td>{{ $item->formatted_line_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row"><span>Subtotal</span><span>{{ $order->formatted_subtotal }}</span></div>
        <div class="totals-row"><span>Ongkos Kirim</span><span>{{ $order->formatted_shipping }}</span></div>
        <div class="totals-row grand"><span>Total</span><span>{{ $order->formatted_total }}</span></div>
    </div>

    @if($order->customer_notes)
        <div style="margin-top:28px;padding:14px 16px;background:#faf5f7;border-left:3px solid #f4518c;border-radius:4px;font-size:12px;color:#737380;">
            <strong style="color:#b82d5c;">Catatan:</strong> {{ $order->customer_notes }}
        </div>
    @endif

    <div class="footer">
        Skinbae.ID — Jl. Kecantikan No. 1, Jakarta — www.skinbae.id — invoice ini dihasilkan secara otomatis dan sah.
    </div>
</div>
</body>
</html>
