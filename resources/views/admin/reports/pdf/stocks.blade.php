<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pergerakan Stok</title>
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
        <p>Laporan Pergerakan Stok</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th class="text-center">Total Pengurangan</th>
                <th class="text-center">Sisa Stok Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalDecreased = 0; @endphp
            @forelse($stockMovements as $sm)
            @php 
                $totalDecreased += $sm->total_decreased;
            @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $sm->menu ? $sm->menu->name : 'Unknown' }}</td>
                <td>{{ $sm->menu && $sm->menu->category ? $sm->menu->category->name : '-' }}</td>
                <td class="text-center">{{ $sm->total_decreased }}</td>
                <td class="text-center">{{ $sm->menu ? $sm->menu->stock : 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data pengurangan stok pada periode ini.</td>
            </tr>
            @endforelse
            @if($totalDecreased > 0)
            <tr>
                <th colspan="3" class="text-center">TOTAL PENGURANGAN STOK KESELURUHAN</th>
                <th class="text-center">{{ $totalDecreased }}</th>
                <th></th>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
