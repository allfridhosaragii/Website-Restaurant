<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jam Sibuk (Peak Hour)</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; }
        .header p { margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { clear: both; margin-top: 50px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h2>CULINAIRE RESTAURANT</h2>
        <p>Laporan Jam Sibuk (Peak Hour)</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    @php
        $hourlyData = array_fill(0, 24, ['orders' => 0, 'sales' => 0]);
        $totalOrders = 0;
        $totalSales = 0;
        foreach ($peakHours as $ph) {
            $hourlyData[(int)$ph->hour]['orders'] = $ph->total_orders;
            $hourlyData[(int)$ph->hour]['sales'] = $ph->total_sales;
            $totalOrders += $ph->total_orders;
            $totalSales += $ph->total_sales;
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th class="text-center">Jam</th>
                <th class="text-center">Total Transaksi</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < 24; $i++)
            @if($hourlyData[$i]['orders'] > 0)
            <tr>
                <td class="text-center">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:59</td>
                <td class="text-center">{{ $hourlyData[$i]['orders'] }}</td>
                <td class="text-right">{{ number_format($hourlyData[$i]['sales'], 0, ',', '.') }}</td>
            </tr>
            @endif
            @endfor
            @if($totalOrders == 0)
            <tr>
                <td colspan="3" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @else
            <tr>
                <th class="text-center">TOTAL KESELURUHAN</th>
                <th class="text-center">{{ $totalOrders }}</th>
                <th class="text-right">{{ number_format($totalSales, 0, ',', '.') }}</th>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
