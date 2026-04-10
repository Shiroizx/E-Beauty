<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Pengiriman</title>
    <style>
        @@import url('https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@@0,400;0,700;1,400&family=Inter:wght@@400;600;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #000;
        }

        .print-container {
            margin: 0 auto;
        }

        /* Tampilan Layar */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #ff6b9d;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 14px;
        }
        
        .btn-outline {
            background: transparent;
            color: #ff6b9d;
            border: 1px solid #ff6b9d;
            margin-right: 10px;
        }

        /* Thermal Format (80mm width) */
        @@media screen and (max-width: 400px), .format-thermal {
            body { font-family: 'Courier Prime', monospace; }
            .receipt {
                width: 80mm;
                margin: 20px auto;
                padding: 5mm;
                background: white;
                font-size: 12px;
                line-height: 1.2;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                page-break-after: always;
            }
            .text-center { text-align: center; }
            .fw-bold { font-weight: bold; }
            .mb-1 { margin-bottom: 5px; }
            .mb-2 { margin-bottom: 10px; }
            .mt-2 { margin-top: 10px; }
            .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
            .border-top { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
            .flex-between { display: flex; justify-content: space-between; }
            .small { font-size: 10px; }
            .qr-code { text-align: center; margin: 10px 0; }
            .qr-code svg { width: 100px; height: 100px; }
        }

        /* A4 Format */
        .format-a4 {
            font-family: 'Inter', sans-serif;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 210mm;
            margin: 20px auto;
            padding: 20px;
            background: white;
        }
        
        .format-a4 .receipt {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            page-break-inside: avoid;
            background: white;
        }

        .format-a4 .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .format-a4 .brand { font-size: 20px; font-weight: bold; }
        .format-a4 .qr-wrapper { text-align: right; }
        .format-a4 .qr-wrapper svg { width: 60px; height: 60px; }
        
        .format-a4 .section { margin-bottom: 15px; }
        .format-a4 .section-title { font-weight: bold; text-transform: uppercase; font-size: 11px; color: #666; border-bottom: 1px solid #eee; margin-bottom: 5px; padding-bottom: 3px; }
        
        .format-a4 table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .format-a4 th, .format-a4 td { padding: 5px; text-align: left; border-bottom: 1px solid #eee; }
        .format-a4 th { font-weight: bold; background: #f9f9f9; }
        .format-a4 .text-right { text-align: right; }

        @@media print {
            .controls { display: none; }
            body { background: white; margin: 0; padding: 0; }
            .print-container { margin: 0; }
            .format-thermal .receipt { margin: 0; box-shadow: none; width: 72mm; padding: 0; }
            .format-a4 { margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body class="format-{{ $format }}">

    <div class="controls">
        <button type="button" class="btn btn-outline" onclick="window.close()">Tutup</button>
        <button type="button" class="btn" onclick="window.print()">Cetak Sekarang</button>
    </div>

    <div class="print-container">
        @foreach($orders as $order)
            @if($format === 'thermal')
                <!-- Thermal Layout -->
                <div class="receipt">
                    <div class="text-center fw-bold" style="font-size: 16px;">SKINBAE.ID</div>
                    <div class="text-center small border-bottom">Premium Beauty & Skincare</div>
                    
                    <div class="text-center mt-2 mb-2 fw-bold">
                        RESI PENGIRIMAN
                    </div>

                    <div class="qr-code">
                        {!! QrCode::size(100)->generate(url('/track/'.$order->order_number)) !!}
                    </div>

                    <div class="text-center fw-bold mb-2">{{ $order->order_number }}</div>

                    <div class="border-bottom">
                        <div class="small">Kurir:</div>
                        <div class="fw-bold">{{ strtoupper($order->shipping_courier ?? 'REG') }} - {{ strtoupper($order->shipping_service ?? 'Standard') }}</div>
                    </div>

                    <div class="mt-2 border-bottom">
                        <div class="small">Penerima:</div>
                        <div class="fw-bold">{{ $order->shipping_name }}</div>
                        <div>{{ $order->shipping_phone }}</div>
                        <div class="small mt-1">{{ $order->shipping_address_line }}</div>
                        <div class="small">{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</div>
                    </div>

                    <div class="mt-2 border-bottom">
                        <div class="small">Detail Produk:</div>
                        @foreach($order->items as $item)
                            <div class="flex-between mt-1">
                                <div style="max-width: 75%;">{{ $item->product_name }}</div>
                                <div>x{{ $item->quantity }}</div>
                            </div>
                            <div class="small text-right">{{ $item->formatted_line_total }}</div>
                        @endforeach
                    </div>

                    <div class="mt-2 border-bottom">
                        <div class="flex-between">
                            <span>Subtotal:</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if(($order->discount_amount ?? 0) > 0)
                            <div class="flex-between">
                                <span>Diskon promo @isset($order->promo_code)({{ $order->promo_code }}) @endisset:</span>
                                <span>− {{ $order->formatted_discount_amount }}</span>
                            </div>
                        @endif
                        <div class="flex-between">
                            <span>Ongkir:</span>
                            <span>{{ $order->formatted_shipping }}</span>
                        </div>
                        <div class="flex-between fw-bold mt-1">
                            <span>TOTAL:</span>
                            <span>{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    <div class="mt-2 text-center small">
                        <div>Pembayaran: {{ $order->paymentMethodLabel() }}</div>
                        <div class="fw-bold">{{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</div>
                    </div>

                    <div class="text-center small mt-2">
                        *** Terima Kasih ***
                    </div>
                </div>
            @else
                <!-- A4 Layout -->
                <div class="receipt">
                    <div class="header">
                        <div>
                            <div class="brand">SKINBAE.ID</div>
                            <div style="font-size: 11px; color: #666;">Resi Pengiriman</div>
                        </div>
                        <div class="qr-wrapper">
                            {!! QrCode::size(60)->generate(url('/track/'.$order->order_number)) !!}
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 11px; color: #666;">No. Pesanan</div>
                            <div style="font-weight: bold; font-size: 16px;">{{ $order->order_number }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; color: #666;">Kurir</div>
                            <div style="font-weight: bold; font-size: 14px;">{{ strtoupper($order->shipping_courier ?? 'REG') }} - {{ strtoupper($order->shipping_service ?? 'Standard') }}</div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section-title">Penerima</div>
                        <div style="font-weight: bold; font-size: 14px;">{{ $order->shipping_name }}</div>
                        <div>{{ $order->shipping_phone }}</div>
                        <div style="margin-top: 5px;">{{ $order->shipping_address_line }}</div>
                        <div>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</div>
                    </div>

                    <div class="section">
                        <div class="section-title">Detail Pesanan</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div>{{ $item->product_name }}</div>
                                        <div style="font-size: 10px; color: #666;">SKU: {{ $item->product_sku ?? '-' }}</div>
                                    </td>
                                    <td class="text-right" style="font-weight: bold;">{{ $item->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #000; padding-top: 10px; margin-top: 10px;">
                        <div>
                            <div style="font-size: 11px; color: #666;">Metode Pembayaran</div>
                            <div style="font-weight: bold;">{{ $order->paymentMethodLabel() }} ({{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }})</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; color: #666;">Total Harga</div>
                            <div style="font-weight: bold; font-size: 16px;">{{ $order->formatted_total }}</div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <script>
        // Auto print dialogue
        window.onload = function() {
            // setTimeout(() => window.print(), 500);
        };
    </script>
</body>
</html>