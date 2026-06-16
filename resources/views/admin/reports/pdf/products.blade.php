<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk Terlaris</title>
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
        <p>Laporan Produk Terlaris (Top 10)</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Rank</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th class="text-center">Total Terjual</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @forelse($topProducts as $item)
            <tr>
                <td class="text-center">{{ $rank++ }}</td>
                <td>{{ $item->menu ? $item->menu->name : 'Unknown Menu' }}</td>
                <td>{{ $item->menu && $item->menu->category ? $item->menu->category->name : '-' }}</td>
                <td class="text-center">{{ $item->total_qty }}</td>
                <td class="text-right">{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data penjualan produk pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
