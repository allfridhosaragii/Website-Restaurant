@extends('layouts.admin')
@section('title', __('messages.admin_dashboard'))
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-white mb-2">
                                <i class="bi bi-shield-check me-2"></i><span data-i18n="admin_dashboard">{{ __('messages.admin_dashboard') }}</span>
                            </h3>
                            <p class="opacity-75 mb-0">
                                {{ __('messages.admin_welcome_desc', ['name' => Auth::user()->name]) }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success" id="live-indicator"><i class="bi bi-circle-fill text-white blink me-1"></i>LIVE</span>
                            <select id="period-filter" class="form-select form-select-sm" style="width: auto;">
                                <option value="today">Hari Ini</option>
                                <option value="yesterday">Kemarin</option>
                                <option value="7days">7 Hari Terakhir</option>
                                <option value="30days">30 Hari Terakhir</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if(isset($lowStockMenus) && $lowStockMenus->isNotEmpty())
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan Stok Menipis</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($lowStockMenus as $lowMenu)
                            <li>
                                <strong>{{ $lowMenu->name }}</strong>: Sisa stok {{ $lowMenu->stock }} (Batas minimum: {{ $lowMenu->min_stock }})
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 border-start border-4 border-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pendapatan</small>
                            <h4 class="mb-0 text-success" id="total-revenue">Rp 0</h4>
                        </div>
                        <i class="bi bi-cash-stack fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-start border-4 border-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pesanan</small>
                            <h4 class="mb-0 text-info" id="total-orders">0</h4>
                        </div>
                        <i class="bi bi-bag-check fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-start border-4 border-warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pesanan Pending</small>
                            <h4 class="mb-0 text-warning" id="pending-orders">0</h4>
                        </div>
                        <i class="bi bi-clock-history fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Grafik Penjualan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-star me-2 text-warning"></i>Top 5 Menu</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topMenusChart" height="200"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-layers me-2 text-success"></i>Top Modifiers</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="top-modifiers-list">
                            <!-- Injected via JS -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-bag text-primary me-2"></i>10 Pesanan Terbaru
                        </h5>
                        <a href="/admin/orders" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="recent-orders-list">
                            <div class="p-4 text-center text-muted">
                                <div class="spinner-border text-primary" role="status">
                                  <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<style>
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let salesChart = null;
        let topMenusChart = null;
        
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }

        function initCharts() {
            const ctxSales = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(ctxSales, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Pendapatan', data: [], borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });

            const ctxMenus = document.getElementById('topMenusChart').getContext('2d');
            topMenusChart = new Chart(ctxMenus, {
                type: 'doughnut',
                data: { labels: [], datasets: [{ data: [], backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'] }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        async function fetchLiveData() {
            const period = document.getElementById('period-filter').value;
            try {
                const response = await fetch(`/admin/dashboard/live-data?period=${period}`);
                const data = await response.json();
                
                // Update Summary
                document.getElementById('total-revenue').innerText = formatRupiah(data.summary.total_revenue);
                document.getElementById('total-orders').innerText = data.summary.total_orders;
                document.getElementById('pending-orders').innerText = data.summary.pending_orders;

                // Update Sales Chart
                salesChart.data.labels = data.salesChart.labels;
                salesChart.data.datasets[0].data = data.salesChart.data;
                salesChart.update();

                // Update Top Menus Chart
                topMenusChart.data.labels = data.topMenus.map(m => m.name);
                topMenusChart.data.datasets[0].data = data.topMenus.map(m => m.total_qty);
                topMenusChart.update();

                // Update Modifiers
                const modifiersList = document.getElementById('top-modifiers-list');
                modifiersList.innerHTML = '';
                if (data.topModifiers.length > 0) {
                    data.topModifiers.forEach(mod => {
                        modifiersList.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">${mod.name}<span class="badge bg-primary rounded-pill">${mod.count}</span></li>`;
                    });
                } else {
                    modifiersList.innerHTML = '<li class="list-group-item text-center text-muted">Belum ada data</li>';
                }

                // Update Recent Orders
                const ordersList = document.getElementById('recent-orders-list');
                ordersList.innerHTML = '';
                if (data.recentOrders.length > 0) {
                    data.recentOrders.forEach(order => {
                        let badge = '';
                        if(order.status === 'completed') badge = '<span class="badge bg-success">Selesai</span>';
                        else if(order.status === 'processing') badge = '<span class="badge bg-info">Diproses</span>';
                        else if(order.status === 'pending') badge = '<span class="badge bg-warning text-dark">Pending</span>';
                        else badge = '<span class="badge bg-danger">Batal</span>';

                        ordersList.innerHTML += `
                            <a href="/admin/orders/${order.id}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#${order.order_number}</strong>
                                        <small class="text-muted d-block">${order.customer_name || 'Guest'}</small>
                                    </div>
                                    <div class="text-end">
                                        ${badge}
                                        <small class="d-block text-muted">${formatRupiah(order.total)}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    ordersList.innerHTML = '<div class="p-4 text-center text-muted"><i class="bi bi-inbox fs-1"></i><p class="mb-0">Belum ada pesanan</p></div>';
                }

            } catch (error) {
                console.error("Failed to fetch live data:", error);
            }
        }

        initCharts();
        fetchLiveData();

        // Polling every 30 seconds
        setInterval(fetchLiveData, 30000);

        // Filter change
        document.getElementById('period-filter').addEventListener('change', fetchLiveData);
    });
</script>
@endpush
@endsection