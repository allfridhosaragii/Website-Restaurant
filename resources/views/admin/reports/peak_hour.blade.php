@extends('admin.reports._layout', ['title' => 'Laporan Jam Sibuk (Peak Hour)'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Jam</th>
            <th class="text-center">Total Transaksi</th>
            <th class="text-end">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < 24; $i++)
        @if($dataOrders[$i] > 0)
        <tr>
            <td class="fw-bold">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:59</td>
            <td class="text-center">
                <span class="badge bg-primary rounded-pill fs-6">{{ $dataOrders[$i] }}</span>
            </td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($dataSales[$i], 0, ',', '.') }}</td>
        </tr>
        @endif
        @endfor
        
        @if(array_sum($dataOrders) == 0)
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data transaksi pada periode ini
            </td>
        </tr>
        @endif
    </tbody>
</table>
@endsection

@section('report_script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: {!! json_encode($dataSales) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Transaksi',
                        data: {!! json_encode($dataOrders) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        type: 'bar',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
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
