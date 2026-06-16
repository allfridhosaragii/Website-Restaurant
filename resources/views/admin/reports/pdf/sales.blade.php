<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
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
        .summary { float: right; width: 300px; }
        .summary table { border: none; }
        .summary th, .summary td { border: none; padding: 4px; }
        .summary th { text-align: left; background-color: transparent; }
        .footer { clear: both; margin-top: 50px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h2>CULINAIRE RESTAURANT</h2>
        <p>Laporan Penjualan</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID Pesanan</th>
                <th>Tipe</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRevenue = 0; $no = 1; @endphp
            @forelse($sales as $order)
            @php $totalRevenue += $order->total; @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->type == 'dine_in' ? 'Dine In' : 'Takeaway' }}</td>
                <td>{{ $order->customer ? $order->customer->name : 'Walk-in' }}</td>
                <td>{{ $order->cashier ? $order->cashier->name : '-' }}</td>
                <td class="text-right">{{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <th>Total Transaksi:</th>
                <td class="text-right">{{ $sales->count() }}</td>
            </tr>
            <tr>
                <th>Total Pendapatan:</th>
                <td class="text-right"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <th>Rata-rata / Transaksi:</th>
                <td class="text-right">Rp {{ $sales->count() > 0 ? number_format($totalRevenue / $sales->count(), 0, ',', '.') : 0 }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
