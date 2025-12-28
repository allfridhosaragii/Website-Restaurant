@extends('layouts.admin')
@section('title', 'Laporan Transaksi')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3 class="mb-1">Laporan Transaksi</h3>
                        <p class="text-muted mb-0">Semua transaksi pesanan</p>
                    </div>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="bi bi-file-earmark-text me-2"></i>Lihat e-Statement
                    </a>
                </div>
            </div>
        </div>

        <!-- Month Tabs -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex gap-2 flex-nowrap align-items-center overflow-auto no-scrollbar" id="monthTabsContainer" style="justify-content: flex-start; scroll-behavior: smooth;">
                    <!-- Spacer to push items to right on desktop if needed, but for scrollable we want natural flow -->
                    <div class="flex-grow-1 d-none d-md-block"></div>
                    
                    @foreach($monthTabs as $tab)
                    <button onclick="loadData({{ $tab['month'] }}, {{ $tab['year'] }}, this)" 
                       class="btn rounded-pill px-4 month-tab flex-shrink-0 {{ $tab['active'] ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">
                        {{ $tab['label'] }}
                    </button>
                    @endforeach
                    <button class="btn btn-outline-secondary rounded-pill px-3 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-primary" id="totalOrders">{{ $totalOrders }}</h4>
                    <small class="text-muted">Total Pesanan</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-success" id="totalRevenue">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total Pendapatan</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-warning" id="inProcess">{{ $statusStats['pending'] + $statusStats['processing'] }}</h4>
                    <small class="text-muted">Dalam Proses</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-info" id="completed">{{ $statusStats['completed'] }}</h4>
                    <small class="text-muted">Selesai</small>
                </div>
            </div>
        </div>

        <!-- Chart and Stats -->
        <div class="row g-2 g-md-4 mb-4">
            <div class="col-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent chart-card-header px-2 py-2">
                        <h6 class="mb-0 text-truncate">Status Pesanan</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center chart-card-body p-2">
                        <canvas id="statusChart" style="max-height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent chart-card-header px-2 py-2">
                        <h6 class="mb-0 text-truncate">Status Pembayaran</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center chart-card-body p-2">
                        <canvas id="paymentChart" style="max-height: 250px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">Daftar Transaksi</h6>
            </div>
            <div class="card-body p-0">
                <div class="scroll-container no-scrollbar">
                    <table class="table table-hover mb-0 table-mobile-scroll" style="min-width: 800px;">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody">
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <strong>#{{ substr($order->order_number ?? $order->id, 0, 8) }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->customer_name }}</strong>
                                        <br><small class="text-muted">{{ $order->customer_email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->item_count }} item</span>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info">Diproses</span>
                                    @elseif($order->status == 'pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Belum</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="text-end">
                                    <a href="/admin/orders/{{ $order->id }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mb-0">Tidak ada transaksi pada periode ini</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-4">
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</section>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-select">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    /* Mobile improvements for chart cards */
    @media (max-width: 768px) {
        .chart-card-header h6 {
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chart-card-body {
            padding: 0.5rem !important;
        }
        canvas {
            max-height: 200px !important;
        }
    }
    
    /* Force table to be scrollable on mobile */
    .table-mobile-scroll th,
    .table-mobile-scroll td {
        white-space: nowrap;
    }
    .scroll-container {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@push('scripts')
<script>
    let statusChart = null;
    let paymentChart = null;

    // Helper to convert hex to rgba
    function hexToRgba(hex, alpha) {
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // Custom Plugin for Glow/Shadow Effect
    const glowPlugin = {
        id: 'glowEffect',
        beforeDatasetsDraw(chart, args, options) {
            const { ctx } = chart;
            const activeIndex = chart.config.options.activeIndex;
            
            if (typeof activeIndex === 'number' && activeIndex >= 0) {
                const meta = chart.getDatasetMeta(0);
                const arc = meta.data[activeIndex];
                
                if (arc) {
                    ctx.save();
                    const model = arc.getProps(['x', 'y', 'startAngle', 'endAngle', 'outerRadius', 'innerRadius', 'options'], true);
                    
                    ctx.beginPath();
                    ctx.arc(model.x, model.y, model.outerRadius, model.startAngle, model.endAngle);
                    ctx.arc(model.x, model.y, model.innerRadius, model.endAngle, model.startAngle, true);
                    ctx.closePath();
                    
                    ctx.fillStyle = model.options.backgroundColor;
                    ctx.shadowColor = model.options.backgroundColor;
                    ctx.shadowBlur = 20; // Soft glow
                    ctx.shadowOffsetX = 0;
                    ctx.shadowOffsetY = 0;
                    
                    ctx.fill();
                    ctx.restore();
                }
            }
        }
    };

    // Scroll to right on load
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('monthTabsContainer');
        if(container) {
            container.scrollLeft = container.scrollWidth;
        }
    });

    // Initial Charts
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Diproses', 'Menunggu', 'Dibatalkan'],
            datasets: [{
                data: [{{ $statusStats['completed'] }}, {{ $statusStats['processing'] }}, {{ $statusStats['pending'] }}, {{ $statusStats['cancelled'] }}],
                backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#dc3545'],
                borderWidth: 0,
                cutout: '65%',
                hoverOffset: 4
            }]
        },
        plugins: [glowPlugin],
        options: {
            activeIndex: -1, // Custom state
            responsive: true,
            maintainAspectRatio: true,
            layout: { padding: 20 }, // Extra padding for shadow
            onClick: (e, elements, chart) => {
                const newIndex = elements[0] ? elements[0].index : -1;
                
                // Toggle: if clicking same index, deselect (set to -1). Else set to new index.
                if (chart.config.options.activeIndex === newIndex) {
                    chart.config.options.activeIndex = -1;
                } else {
                    chart.config.options.activeIndex = newIndex;
                }
                chart.update();
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        padding: 5, 
                        usePointStyle: true,
                        boxWidth: 6,
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    paymentChart = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Bayar'],
            datasets: [{
                data: [{{ $paymentStats['paid'] }}, {{ $paymentStats['unpaid'] }}],
                backgroundColor: ['#fd7e14', '#7c3aed'],
                borderWidth: 0,
                cutout: '70%',
                hoverOffset: 4
            }]
        },
        plugins: [glowPlugin],
        options: {
            activeIndex: -1,
            responsive: true,
            maintainAspectRatio: true,
            layout: { padding: 20 },
            onClick: (e, elements, chart) => {
                const newIndex = elements[0] ? elements[0].index : -1;
                if (chart.config.options.activeIndex === newIndex) {
                    chart.config.options.activeIndex = -1;
                } else {
                    chart.config.options.activeIndex = newIndex;
                }
                chart.update();
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        padding: 5, 
                        usePointStyle: true,
                        boxWidth: 6,
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // Function to load data via AJAX
    function loadData(month, year, button) {
        // Update tabs UI
        document.querySelectorAll('.month-tab').forEach(btn => {
            btn.classList.remove('btn-warning', 'text-dark');
            btn.classList.add('btn-outline-secondary');
        });
        if(button) {
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-warning', 'text-dark');
        }

        // Show loading state (optional)
        document.getElementById('transactionTableBody').style.opacity = '0.5';

        fetch(`/admin/report/api?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                // Update Stats
                document.getElementById('totalOrders').innerText = data.totalOrders;
                document.getElementById('totalRevenue').innerText = data.formattedRevenue;
                document.getElementById('inProcess').innerText = data.inProcess;
                document.getElementById('completed').innerText = data.statusStats.completed;

                // Update Charts
                statusChart.data.datasets[0].data = [
                    data.statusStats.completed,
                    data.statusStats.processing,
                    data.statusStats.pending,
                    data.statusStats.cancelled
                ];
                statusChart.update();

                paymentChart.data.datasets[0].data = [
                    data.paymentStats.paid, 
                    data.paymentStats.unpaid
                ];
                paymentChart.update();

                // Update Table
                const tbody = document.getElementById('transactionTableBody');
                tbody.innerHTML = '';

                if (data.orders.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mb-0">Tidak ada transaksi pada periode ini</p>
                            </td>
                        </tr>
                    `;
                } else {
                    data.orders.forEach(order => {
                        let statusBadge = '';
                        if (order.status == 'completed') statusBadge = '<span class="badge bg-success">Selesai</span>';
                        else if (order.status == 'processing') statusBadge = '<span class="badge bg-info">Diproses</span>';
                        else if (order.status == 'pending') statusBadge = '<span class="badge bg-warning text-dark">Menunggu</span>';
                        else statusBadge = '<span class="badge bg-danger">Dibatalkan</span>';

                        let paymentBadge = '';
                        if (order.payment_status == 'paid') paymentBadge = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lunas</span>';
                        else paymentBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Belum</span>';

                        const tr = `
                            <tr>
                                <td><strong>#${(order.order_number || order.id).substring(0, 8)}</strong></td>
                                <td>
                                    <div>
                                        <strong>${order.customer_name}</strong>
                                        <br><small class="text-muted">${order.customer_email}</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">${order.item_count} item</span></td>
                                <td><strong>${order.formatted_total}</strong></td>
                                <td>${statusBadge}</td>
                                <td>${paymentBadge}</td>
                                <td>${order.formatted_date}</td>
                                <td class="text-end">
                                    <a href="/admin/orders/${order.id}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += tr;
                    });
                }
                
                document.getElementById('transactionTableBody').style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('transactionTableBody').style.opacity = '1';
                alert('Gagal memuat data');
            });
    }
</script>
@endpush
