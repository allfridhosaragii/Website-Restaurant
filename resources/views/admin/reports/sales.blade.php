@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <a href="/admin/reports" class="text-decoration-none mb-3 d-inline-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard Laporan
            </a>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-primary">Laporan Penjualan</h4>
                <div class="d-flex gap-2">
                    <a href="?start_date={{ $start }}&end_date={{ $end }}&export=excel" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <a href="?start_date={{ $start }}&end_date={{ $end }}&export=pdf" class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
            
            <!-- Filter Tanggal -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form action="" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $end }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-filter"></i> Filter Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Ringkasan Singkat -->
            <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold">Total Penjualan (Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }})</h6>
                </div>
                <h4 class="mb-0 fw-bold text-primary">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
            </div>

            <!-- Chart -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Grafik Penjualan</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="salesChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            <!-- Tabel Detail -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Detail Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Waktu</th>
                                    <th>Tipe</th>
                                    <th>Pelanggan</th>
                                    <th>Kasir</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($order->type == 'dine_in')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Dine In</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Takeaway</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->customer ? $order->customer->name : 'Walk-in' }}</td>
                                    <td>{{ $order->cashier ? $order->cashier->name : '-' }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! json_encode($data) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
