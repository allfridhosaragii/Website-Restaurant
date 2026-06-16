<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Metode Pembayaran</title>
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
        <p>Laporan Metode Pembayaran</p>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Metode Pembayaran</th>
                <th class="text-center">Total Transaksi</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalCount = 0; $totalAmount = 0; @endphp
            @forelse($paymentMethods as $pm)
            @php 
                $totalCount += $pm->count; 
                $totalAmount += $pm->total;
            @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $pm->payment_method)) }}</td>
                <td class="text-center">{{ $pm->count }}</td>
                <td class="text-right">{{ number_format($pm->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pembayaran pada periode ini.</td>
            </tr>
            @endforelse
            @if($totalCount > 0)
            <tr>
                <th colspan="2" class="text-center">TOTAL KESELURUHAN</th>
                <th class="text-center">{{ $totalCount }}</th>
                <th class="text-right">{{ number_format($totalAmount, 0, ',', '.') }}</th>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
