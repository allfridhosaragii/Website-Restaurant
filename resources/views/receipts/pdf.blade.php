<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        
        .receipt-header h3 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .receipt-header p { margin: 2px 0; font-size: 10px; }
        
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 2px 0; vertical-align: top; }
        
        .item-row td { padding-bottom: 2px; }
        .modifier-row td { font-size: 10px; color: #333; padding-left: 5px; }
        
        .totals-table td { padding: 1px 0; }
        .grand-total { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>

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
