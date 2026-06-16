@extends('admin.reports._layout', ['title' => 'Produk Terlaris'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Rank</th>
            <th>Nama Menu</th>
            <th>Kategori</th>
            <th class="text-center">Total Terjual</th>
            <th class="text-end">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @php $rank = 1; @endphp
        @forelse($topProducts as $item)
        <tr>
            <td>
                @if($rank <= 3)
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-trophy-fill"></i> {{ $rank++ }}</span>
                @else
                    <span class="badge bg-secondary">{{ $rank++ }}</span>
                @endif
            </td>
            <td class="fw-bold">{{ $item->menu ? $item->menu->name : 'Unknown Menu' }}</td>
            <td>{{ $item->menu && $item->menu->category ? $item->menu->category->name : '-' }}</td>
            <td class="text-center"><span class="badge bg-primary rounded-pill fs-6">{{ $item->total_qty }}</span></td>
            <td class="text-end fw-bold text-success">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data produk pada periode ini
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
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: {!! json_encode($data) !!},
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
