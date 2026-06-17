<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/struk.css') }}">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 10px; padding: 10px; background: #f0f0f0;">
        <button onclick="window.print();" style="padding: 5px 15px; background: #28a745; color: #fff; border: none; border-radius: 3px; cursor: pointer;">Print</button>
        <button onclick="window.close();" style="padding: 5px 15px; background: #dc3545; color: #fff; border: none; border-radius: 3px; cursor: pointer;">Tutup</button>
    </div>

    <script>
        // Read printer size from localStorage (default 58mm)
        let printerSize = localStorage.getItem('printer_size') || '58';
        document.body.style.width = printerSize + 'mm';

        // Auto print after a small delay to let CSS load
        setTimeout(() => {
            window.print();
        }, 500);
    </script>

    <div class="text-center receipt-header mb-2">
        <h3>RESTORAN MANTAP</h3>
        <p>Jl. Contoh Alamat No. 123, Kota</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="divider"></div>

    <table style="font-size: 10px; margin-bottom: 5px;">
        <tr>
            <td>Order</td>
            <td>: {{ $order->order_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>: -</td>
        </tr>
        <tr>
            <td>Tipe</td>
            <td>: {{ $order->type === 'dine_in' ? 'Dine In' : 'Take Away' }}</td>
        </tr>
        @if($order->table_number)
        <tr>
            <td>Meja</td>
            <td>: {{ $order->table_number }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table class="mb-1">
        @foreach($order->items as $item)
        <tr class="item-row">
            <td style="width: 15%;">{{ $item->quantity }}x</td>
            <td style="width: 50%;">{{ $item->menu_name }}</td>
            <td class="text-right" style="width: 35%;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
        </tr>
        
        @php $mods = $item->modifiers ? json_decode($item->modifiers, true) : null; @endphp
        @if($mods && is_array($mods))
            @foreach($mods as $mod)
            <tr class="modifier-row">
                <td></td>
                <td>- {{ $mod['name'] }}: {{ $mod['option_name'] }}</td>
                <td class="text-right">{{ $mod['price'] > 0 ? '+'.number_format($mod['price'], 0, ',', '.') : '' }}</td>
            </tr>
            @endforeach
        @endif
        @endforeach
    </table>

    <div class="divider"></div>

    <table class="totals-table mb-2">
        @php $subtotal = $order->subtotal_before_discount ?? $order->subtotal; @endphp
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td>Pajak (10%)</td>
            <td class="text-right">{{ number_format($order->tax, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total mt-1">
            <td class="font-bold">TOTAL</td>
            <td class="text-right font-bold">{{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>
    
    <div class="text-center mt-2 mb-2" style="font-size: 10px;">
        <p class="mb-1">Metode Bayar: <strong>{{ strtoupper($order->payment_status == 'paid' ? 'QRIS / Cash' : 'Pending') }}</strong></p>
        <p>Terima Kasih!</p>
    </div>

</body>
</html>
