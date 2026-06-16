@extends('layouts.admin')
@section('title', 'Laporan & Statistik')
@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4 text-primary">Dashboard Laporan</h4>
        </div>
        
        <!-- Summary Cards -->
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card sales-card h-100 shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <h5 class="card-title text-muted fw-bold mb-1">Penjualan Hari Ini</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card revenue-card h-100 shadow-sm border-0 border-start border-success border-4">
                <div class="card-body">
                    <h5 class="card-title text-muted fw-bold mb-1">Total Transaksi</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 48px; height: 48px;">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div class="ps-3">
                            <h4 class="mb-0 fw-bold">{{ $totalTransaksi }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card revenue-card h-100 shadow-sm border-0 border-start border-info border-4">
                <div class="card-body">
                    <h5 class="card-title text-muted fw-bold mb-1">Rata-rata Transaksi</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 48px; height: 48px;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="ps-3">
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($rataRataPerTransaksi, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card info-card customers-card h-100 shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title text-muted fw-bold mb-1">Pelanggan Baru</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 48px; height: 48px;">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h4 class="mb-0 fw-bold">{{ $pelangganBaru }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Penjualan Minggu Ini -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Grafik Penjualan Minggu Ini</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="salesChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Laporan -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 text-secondary">Menu Laporan Detail</h5>
        </div>
        
        @php
        $menus = [
            ['title' => 'Laporan Penjualan', 'icon' => 'bi-graph-up', 'url' => '/admin/reports/sales', 'color' => 'primary', 'desc' => 'Tren harian, mingguan, bulanan'],
            ['title' => 'Produk Terlaris', 'icon' => 'bi-cup-hot', 'url' => '/admin/reports/products', 'color' => 'success', 'desc' => 'Top 10 menu favorit pelanggan'],
            ['title' => 'Peak Hour', 'icon' => 'bi-clock-history', 'url' => '/admin/reports/peak-hour', 'color' => 'warning', 'desc' => 'Waktu terpadat (00:00 - 23:59)'],
            ['title' => 'Metode Pembayaran', 'icon' => 'bi-credit-card', 'url' => '/admin/reports/payments', 'color' => 'info', 'desc' => 'Distribusi tunai, QRIS, dll'],
            ['title' => 'Kinerja Karyawan', 'icon' => 'bi-person-badge', 'url' => '/admin/reports/employees', 'color' => 'danger', 'desc' => 'Penjualan berdasarkan kasir'],
            ['title' => 'Laporan Meja', 'icon' => 'bi-grid-3x3', 'url' => '/admin/reports/tables', 'color' => 'secondary', 'desc' => 'Meja paling sering digunakan'],
            ['title' => 'Diskon & Promo', 'icon' => 'bi-tags', 'url' => '/admin/reports/discounts', 'color' => 'dark', 'desc' => 'Riwayat penggunaan voucher'],
            ['title' => 'Pergerakan Stok', 'icon' => 'bi-box-seam', 'url' => '/admin/reports/stocks', 'color' => 'primary', 'desc' => 'Masuk dan keluar bahan/menu'],
        ];
        @endphp

        @foreach($menus as $menu)
        <div class="col-md-3 mb-4">
            <a href="{{ $menu['url'] }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 report-menu-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-{{ $menu['color'] }} bg-opacity-10 text-{{ $menu['color'] }} mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            <i class="bi {{ $menu['icon'] }}"></i>
                        </div>
                        <h6 class="fw-bold text-dark">{{ $menu['title'] }}</h6>
                        <small class="text-muted">{{ $menu['desc'] }}</small>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</section>

<style>
    .report-menu-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

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
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
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
                            callback: function(value, index, values) {
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