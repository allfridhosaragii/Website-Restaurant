<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan per Meja</title>
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
        <p>Laporan Penjualan per Meja</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Meja</th>
                <th class="text-center">Total Pesanan</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalOrders = 0; $totalSales = 0; @endphp
            @forelse($tableSales as $ts)
            @php 
                $totalOrders += $ts->total_orders; 
                $totalSales += $ts->total_sales;
            @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $ts->table ? 'Meja ' . $ts->table->table_number : 'Unknown' }}</td>
                <td class="text-center">{{ $ts->total_orders }}</td>
                <td class="text-right">{{ number_format($ts->total_sales, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data penjualan per meja pada periode ini.</td>
            </tr>
            @endforelse
            @if($totalOrders > 0)
            <tr>
                <th colspan="2" class="text-center">TOTAL KESELURUHAN</th>
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
