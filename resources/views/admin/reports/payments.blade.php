@extends('admin.reports._layout', ['title' => 'Metode Pembayaran'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Metode Pembayaran</th>
            <th class="text-center">Total Transaksi</th>
            <th class="text-end">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($paymentMethods as $pm)
        <tr>
            <td class="fw-bold">{{ ucwords(str_replace('_', ' ', $pm->payment_method)) }}</td>
            <td class="text-center"><span class="badge bg-primary rounded-pill fs-6">{{ $pm->count }}</span></td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($pm->total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data pembayaran pada periode ini
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

@section('report_script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    data: {!! json_encode($data) !!},
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(102, 16, 242, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    });
</script>
@endsection
