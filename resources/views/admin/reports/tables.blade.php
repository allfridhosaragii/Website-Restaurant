@extends('admin.reports._layout', ['title' => 'Penjualan per Meja'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Meja</th>
            <th class="text-center">Total Pesanan</th>
            <th class="text-end">Total Pendapatan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tableSales as $ts)
        <tr>
            <td class="fw-bold">{{ $ts->table ? 'Meja ' . $ts->table->table_number : 'Unknown' }}</td>
            <td class="text-center"><span class="badge bg-primary rounded-pill fs-6">{{ $ts->total_orders }}</span></td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($ts->total_sales, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data meja pada periode ini
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
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Total Pendapatan (Rp)',
                        data: {!! json_encode($dataSales) !!},
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Transaksi',
                        data: {!! json_encode($dataOrders) !!},
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
