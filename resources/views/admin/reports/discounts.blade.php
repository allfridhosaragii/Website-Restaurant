@extends('admin.reports._layout', ['title' => 'Penggunaan Diskon & Promo'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Nama Diskon / Promo</th>
            <th class="text-center">Total Digunakan</th>
            <th class="text-end">Total Potongan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($discountUsages as $du)
        <tr>
            <td class="fw-bold">
                {{ $du->discount ? $du->discount->name : 'Unknown' }}
                @if($du->discount)
                <br><small class="text-muted fw-normal">{{ $du->discount->type == 'percentage' ? $du->discount->value . '%' : 'Rp ' . number_format($du->discount->value, 0, ',', '.') }}</small>
                @endif
            </td>
            <td class="text-center"><span class="badge bg-primary rounded-pill fs-6">{{ $du->usage_count }}</span></td>
            <td class="text-end fw-bold text-danger">- Rp {{ number_format($du->total_discount, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data penggunaan diskon pada periode ini
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
                        label: 'Total Potongan (Rp)',
                        data: {!! json_encode($dataAmounts) !!},
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Digunakan',
                        data: {!! json_encode($dataUsages) !!},
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
