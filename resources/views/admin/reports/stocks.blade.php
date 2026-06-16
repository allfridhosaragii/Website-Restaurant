@extends('admin.reports._layout', ['title' => 'Pergerakan Stok (Pengurangan)'])

@section('report_table')
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>Nama Menu</th>
            <th>Kategori</th>
            <th class="text-center">Total Pengurangan (Terjual)</th>
            <th class="text-center">Sisa Stok Saat Ini</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stockMovements as $sm)
        <tr>
            <td class="fw-bold">{{ $sm->menu ? $sm->menu->name : 'Unknown' }}</td>
            <td>{{ $sm->menu && $sm->menu->category ? $sm->menu->category->name : '-' }}</td>
            <td class="text-center text-danger fw-bold">-{{ $sm->total_decreased }}</td>
            <td class="text-center">
                @if($sm->menu)
                    @if($sm->menu->stock <= 5)
                        <span class="badge bg-danger">{{ $sm->menu->stock }}</span>
                    @else
                        <span class="badge bg-success">{{ $sm->menu->stock }}</span>
                    @endif
                @else
                    -
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Tidak ada data pengurangan stok pada periode ini
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
                        label: 'Total Pengurangan Stok',
                        data: {!! json_encode($data) !!},
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    }
                ]
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
