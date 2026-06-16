<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran - {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Terima kasih, {{ $order->customer_name }}!</h2>
        <p>Pesanan Anda dengan nomor <strong>{{ $order->order_number }}</strong> telah berhasil diproses dan dibayar lunas.</p>
        <p>Kami telah melampirkan struk pembayaran pada email ini untuk referensi Anda.</p>
        
        <div style="background: #f8f9fa; padding: 15px; margin-top: 20px; border-radius: 5px;">
            <h4 style="margin-top: 0;">Ringkasan Pesanan</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0;">Total Pembayaran:</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;">Tanggal:</td>
                    <td style="text-align: right;">{{ date('d M Y H:i', strtotime($order->created_at)) }}</td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 0.9em; color: #666;">
            Jika Anda memiliki pertanyaan terkait pesanan ini, silakan hubungi layanan pelanggan kami.<br>
            Salam hangat,<br>
            Tim Restoran Kami
        </p>
    </div>
</body>
</html>
