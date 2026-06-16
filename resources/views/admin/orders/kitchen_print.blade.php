<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Kitchen - Order #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14pt;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .ticket {
            width: 100%;
            max-width: 300px;
            margin: 0 auto 20px auto;
            padding: 10px;
            border: 1px dashed #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .station-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .order-info {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
        }
        .meta-info {
            font-size: 12pt;
            margin-bottom: 10px;
        }
        .item {
            border-bottom: 1px dotted #000;
            padding: 5px 0;
            margin-bottom: 5px;
        }
        .item-qty {
            font-weight: bold;
            font-size: 16pt;
        }
        .item-name {
            font-size: 16pt;
            font-weight: bold;
        }
        .item-mods {
            margin-left: 20px;
            font-size: 12pt;
        }
        .item-notes {
            margin-left: 20px;
            font-size: 12pt;
            font-style: italic;
        }
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .ticket {
                border: none;
                margin: 0;
                padding: 0;
                page-break-after: always;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="padding: 10px; text-align: center; background: #eee; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">Print Lagi</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px;">Tutup</button>
    </div>

    @foreach($stations as $stationName => $items)
        @if(count($items) > 0)
        <div class="ticket">
            <div class="header">
                <div class="station-name">STATION: {{ strtoupper($stationName) }}</div>
                <div class="order-info">Order #{{ $order->order_number }}</div>
            </div>
            
            <div class="meta-info">
                <div>Meja: <strong>{{ $order->table ? $order->table->number : ($order->type === 'take_away' ? 'TAKE AWAY' : '-') }}</strong></div>
                <div>Waktu: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</div>
                <div>Kasir: {{ $order->user ? $order->user->name : '-' }}</div>
            </div>
            
            <div class="items">
                @foreach($items as $item)
                <div class="item">
                    <div>
                        <span class="item-qty">{{ $item->quantity }}x</span>
                        <span class="item-name">{{ $item->menu_name }}</span>
                    </div>
                    
                    @php $mods = $item->modifiers ? json_decode($item->modifiers, true) : null; @endphp
                    @if($mods && is_array($mods))
                        <div class="item-mods">
                            @foreach($mods as $mod)
                                <div>- {{ $mod['option_name'] }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($item->notes)
                        <div class="item-notes">
                            Catatan: {{ $item->notes }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            
            @if($order->notes)
            <div style="margin-top: 10px; border-top: 2px solid #000; padding-top: 5px;">
                <strong>Catatan Order:</strong><br>
                {{ $order->notes }}
            </div>
            @endif
            
            <div style="text-align: center; margin-top: 20px; font-size: 10pt;">
                -- Akhir Pesanan --
            </div>
        </div>
        @endif
    @endforeach

</body>
</html>
