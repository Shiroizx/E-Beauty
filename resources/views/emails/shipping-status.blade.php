<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f4518c; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 30px 20px; border: 1px solid #eee; border-top: none; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; padding: 12px 24px; background: #f4518c; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pembaruan Status Pesanan</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $order->user->name ?? $order->shipping_name }}</strong>,</p>
            <p>Status pesanan Anda <strong>#{{ $order->order_number }}</strong> telah diperbarui menjadi:</p>
            <h3 style="color: #f4518c; text-align: center; padding: 15px; background: #fff5f9; border-radius: 5px;">{{ $statusLabel }}</h3>
            
            <p>Untuk melihat riwayat perjalanan paket dan lokasi terkini, silakan klik tombol di bawah ini:</p>
            <div style="text-align: center;">
                <a href="{{ $trackingUrl }}" class="btn">Lacak Pesanan Saya</a>
            </div>

            <p style="margin-top: 30px;">Terima kasih telah berbelanja di Skinbae.ID!</p>
        </div>
        <div class="footer">
            Email ini dibuat secara otomatis. Harap jangan membalas email ini.
        </div>
    </div>
</body>
</html>