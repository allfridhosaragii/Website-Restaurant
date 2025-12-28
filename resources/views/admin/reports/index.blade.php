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
                    <h4 class="mb-0 text-warning" id="inProcess">{{ $inProcessCount }}</h4>
                    <small class="text-muted">Dalam Proses</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-info" id="completed">{{ $completedCount }}</h4>
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
                        <div class="position-relative w-100" style="min-height: 320px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent chart-card-header px-2 py-2">
                        <h6 class="mb-0 text-truncate">Status Reservasi</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center chart-card-body p-2">
                        <div class="position-relative w-100" style="min-height: 320px;">
                            <canvas id="reservationChart"></canvas>
                        </div>
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
        /* Allow chart to grow */
        canvas {
            max-height: none !important; 
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
    let reservationChart = null;
    let currentMonth = {{ $month }};
    let currentYear = {{ $year }};

    // Helper to convert hex to rgba
    function hexToRgba(hex, alpha) {
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    
    // Registry not needed for hardcoded approach but kept for global click
    // const charts = []; // We will use statusChart and paymentChart variables directly
    
    // Centralized function to set active chart and clear others
    function activateChartSegment(targetChart, index) {
        // Detect mobile
        const isMobile = window.innerWidth < 768;
        const updateMode = isMobile ? 'none' : undefined; // Instant on mobile

        // 1. Set activeIndex for the target chart
        if (targetChart.config.options.activeIndex !== index) {
            targetChart.config.options.activeIndex = index;
        }

        // 2. EXPLICITLY Force reset the OTHER chart
        if (targetChart === statusChart) {
            if (reservationChart && reservationChart.config.options.activeIndex !== -1) {
                reservationChart.config.options.activeIndex = -1;
                resetChartColors(reservationChart); // Reset colors
                reservationChart.update(updateMode);
            }
        } else if (targetChart === reservationChart) {
            if (statusChart && statusChart.config.options.activeIndex !== -1) {
                statusChart.config.options.activeIndex = -1;
                resetChartColors(statusChart); // Reset colors
                statusChart.update(updateMode);
            }
        }
        
        // 3. Update Colors for Dimming Effect
        updateChartColors(targetChart, index);
        targetChart.update(updateMode); // Update the target chart after color change
    }

    function clearAllCharts() {
        const isMobile = window.innerWidth < 768;
        const updateMode = isMobile ? 'none' : undefined;

        if (statusChart && statusChart.config.options.activeIndex !== -1) {
            statusChart.config.options.activeIndex = -1;
            resetChartColors(statusChart);
            statusChart.update(updateMode);
        }
        if (reservationChart && reservationChart.config.options.activeIndex !== -1) {
            reservationChart.config.options.activeIndex = -1;
            resetChartColors(reservationChart);
            reservationChart.update(updateMode);
        }
    }

    // Helper: Reset colors to original solid
    function resetChartColors(chart) {
        if (chart === statusChart) {
             chart.data.datasets[0].backgroundColor = ['#198754', '#dc3545'];
        } else {
             chart.data.datasets[0].backgroundColor = ['#198754', '#ffc107', '#dc3545'];
        }
    }

    // Helper: Dim inactive segments
    function updateChartColors(chart, activeIndex) {
        let originalColors;
        if (chart === statusChart) {
            originalColors = ['#198754', '#dc3545'];
        } else {
            originalColors = ['#198754', '#ffc107', '#dc3545'];
        }

        if (chart.data.datasets[0]) {
             if (activeIndex === -1) {
                chart.data.datasets[0].backgroundColor = [...originalColors];
            } else {
                // Map colors: Active -> Solid, Inactive -> Faded
                chart.data.datasets[0].backgroundColor = originalColors.map((color, i) => {
                    return i === activeIndex ? color : hexToRgba(color, 0.2); // 20% opacity for inactive
                });
            }
        }
    }
    
    // Global click listener to close all effects when clicking/tapping outside charts
    const handleGlobalClick = (e) => {
        // Check if click is inside any chart canvas
        // On mobile, taps on canvas might bubble up, so we need to be careful not to clear immediately if the click originated from interact.
        // But chart.js onClick handles the logic. If we click OUTSIDE chart elements (but on canvas), clearAllCharts is called by chart onClick.
        // If we click COMPLETELY outside canvas, this global listener handles it.
        const isCanvas = e.target.tagName === 'CANVAS';
        
        if (!isCanvas) {
            clearAllCharts();
        }
    };
    
    // Use 'click' which works on both desktop and mobile safely
    document.addEventListener('click', handleGlobalClick);

    // Custom Plugin for Halo/Ring Effect
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
                    
                    // Config for halo
                    const gap = 0; 
                    const ringWidth = 14;
                    const color = model.options.backgroundColor;
                    const ringColor = hexToRgba(color, 0.5); // 50% opacity
                    
                    ctx.beginPath();
                    // Inner edge of ring (starts after gap)
                    ctx.arc(model.x, model.y, model.outerRadius + gap + ringWidth, model.startAngle, model.endAngle);
                    // Outer edge of ring
                    ctx.arc(model.x, model.y, model.outerRadius + gap, model.endAngle, model.startAngle, true);
                    ctx.closePath();
                    
                    ctx.fillStyle = ringColor;
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
            labels: ['Berhasil', 'Gagal'],
            datasets: [{
                data: [{{ $statusStats['success'] }}, {{ $statusStats['failed'] }}],
                backgroundColor: ['#198754', '#dc3545'],
                borderWidth: 0,
                cutout: '65%',
                hoverOffset: 4
            }]
        },
        plugins: [glowPlugin],
        options: {
            activeIndex: -1, // Custom state
            responsive: true,
            maintainAspectRatio: false, // Allow custom height via CSS container
            aspectRatio: window.innerWidth < 768 ? 1 : 2, 
            aspectRatio: window.innerWidth < 768 ? 1 : 2, 
            resizeDelay: 200, 
            animation: window.innerWidth < 768 ? false : {
                duration: 600, 
                easing: 'easeOutQuart'
            },
            animations: {
                colors: false,
                x: false,
                y: false
            },
            layout: { 
                padding: {
                    top: window.innerWidth < 768 ? 20 : 10, 
                    left: window.innerWidth < 768 ? 20 : 10, 
                    right: window.innerWidth < 768 ? 20 : 10, 
                    bottom: window.innerWidth < 768 ? 50 : 30 
                } 
            }, 
            hover: {
                mode: window.innerWidth < 768 ? null : 'nearest',
                intersect: true
            },
            // Fix for "Flickering" (Muncul-Hilang-Muncul):
            // On real mobile devices, a tap triggers touchstart -> mousemove -> click.
            // This causes multiple render cycles. enforcing ONLY 'click' on mobile kills the ghost inputs.
            events: window.innerWidth < 768 ? ['click'] : ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
            onHover: (e, elements, chart) => {
                const isMouse = e.native && e.native.pointerType !== 'touch';
                if (isMouse) {
                    const newIndex = elements[0] ? elements[0].index : -1;
                    activateChartSegment(chart, newIndex);
                }
            },
            onClick: (e, elements, chart) => {
                if (elements[0]) {
                    const newIndex = elements[0].index;
                    // Logic: If clicking same segment -> Toggle off? User said "teken warna lain bakal muncul" (switch).
                    // "teken satu wara ... muncul"
                    // Usually tap same to close is expected on mobile.
                    // Let's check current state.
                    if (chart.config.options.activeIndex === newIndex) {
                        // If already active, close it (toggle)
                         activateChartSegment(chart, -1);
                    } else {
                        // Switch to new
                        activateChartSegment(chart, newIndex);
                    }
                } else {
                    // Click background -> Clear all (handled by global listener usually, but here specific to chart)
                    // If user taps chart background, it IS a chart click, so global listener won't fire.
                    // So we must clear.
                    clearAllCharts();
                }
                // chart.update handled in helper
            },

            plugins: {
                legend: {
                    display: window.innerWidth >= 768, // Hide on mobile, show on desktop
                    position: 'bottom',
                    labels: { 
                        padding: 20, 
                        usePointStyle: true,
                        boxWidth: 6,
                        font: { size: 10 }
                    }
                }
            }
        }
    });



    const reservationCtx = document.getElementById('reservationChart').getContext('2d');
    reservationChart = new Chart(reservationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Berhasil', 'Menunggu', 'Gagal'],
            datasets: [{
                data: [{{ $reservationStats['success'] }}, {{ $reservationStats['pending'] }}, {{ $reservationStats['failed'] }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'], // Green, Yellow, Red
                borderWidth: 0,
                cutout: '70%',
                hoverOffset: 4
            }]
        },
        plugins: [glowPlugin],
        options: {
            activeIndex: -1,
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: window.innerWidth < 768 ? 1 : 2,
            aspectRatio: window.innerWidth < 768 ? 1 : 2,
            resizeDelay: 200,
            animation: window.innerWidth < 768 ? false : {
                duration: 600,
                easing: 'easeOutQuart'
            },
            animations: {
                colors: false,
                x: false,
                y: false
            },
            layout: { 
                padding: {
                    top: window.innerWidth < 768 ? 20 : 10, 
                    left: window.innerWidth < 768 ? 20 : 10, 
                    right: window.innerWidth < 768 ? 20 : 10, 
                    bottom: window.innerWidth < 768 ? 50 : 30 
                } 
            },
            hover: {
                mode: window.innerWidth < 768 ? null : 'nearest',
                intersect: true
            },
            events: window.innerWidth < 768 ? ['click'] : ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
            onHover: (e, elements, chart) => {
                const isMouse = e.native && e.native.pointerType !== 'touch';
                if (isMouse) {
                    const newIndex = elements[0] ? elements[0].index : -1;
                    activateChartSegment(chart, newIndex);
                }
            },
            onClick: (e, elements, chart) => {
                if (elements[0]) {
                    const newIndex = elements[0].index;
                    if (chart.config.options.activeIndex === newIndex) {
                        activateChartSegment(chart, -1);
                    } else {
                        activateChartSegment(chart, newIndex);
                    }
                } else {
                    clearAllCharts();
                }
            },
            plugins: {
                legend: {
                    display: window.innerWidth >= 768, // Hide on mobile, show on desktop
                    position: 'bottom',
                    labels: { 
                        padding: 20, 
                        usePointStyle: true,
                        boxWidth: 6,
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // MOBILE FIX: Explicit touch event handlers that bypass Chart.js event system
    if (window.innerWidth < 768) {
        const statusCanvas = document.getElementById('statusChart');
        const reservationCanvas = document.getElementById('reservationChart');

        function handleChartTouch(e, chart) {
            e.preventDefault();
            e.stopPropagation();
            
            const touch = e.changedTouches[0];
            const rect = e.target.getBoundingClientRect();
            const x = touch.clientX - rect.left;
            const y = touch.clientY - rect.top;
            
            const syntheticEvent = { native: e, x: x, y: y };
            const elements = chart.getElementsAtEventForMode(syntheticEvent, 'nearest', { intersect: true }, false);
            
            if (elements.length > 0) {
                const index = elements[0].index;
                if (chart.config.options.activeIndex === index) {
                    activateChartSegment(chart, -1);
                } else {
                    activateChartSegment(chart, index);
                }
            } else {
                activateChartSegment(chart, -1);
            }
        }

        statusCanvas.addEventListener('touchend', (e) => handleChartTouch(e, statusChart), { passive: false });
        reservationCanvas.addEventListener('touchend', (e) => handleChartTouch(e, reservationChart), { passive: false });
    }

    // Function to load data via AJAX
    function loadData(month, year, button, silent = false) {
        // Update global state
        currentMonth = month;
        currentYear = year;

        // Update tabs UI
        if (button) {
            document.querySelectorAll('.month-tab').forEach(btn => {
                btn.classList.remove('btn-warning', 'text-dark');
                btn.classList.add('btn-outline-secondary');
            });
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-warning', 'text-dark');
        }

        // Show loading state (optional)
        if (!silent) {
            document.getElementById('transactionTableBody').style.opacity = '0.5';
        }

        fetch(`/admin/report/api?month=${month}&year=${year}&t=${new Date().getTime()}`)
            .then(response => response.json())
            .then(data => {
                // Update Stats
                document.getElementById('totalOrders').innerText = data.totalOrders;
                document.getElementById('totalRevenue').innerText = data.formattedRevenue;
                document.getElementById('inProcess').innerText = data.inProcessCount; // Use specific count key
                document.getElementById('completed').innerText = data.completedCount; // Use specific count key

                // Update Charts - PRESERVE ACTIVE STATE
                const statusActiveIndex = statusChart.config.options.activeIndex;
                const reservationActiveIndex = reservationChart.config.options.activeIndex;

                statusChart.data.datasets[0].data = [
                    data.statusStats.success,
                    data.statusStats.failed
                ];
                // Restore original colors before update, then re-apply active state after
                resetChartColors(statusChart);
                statusChart.update('none');
                if (statusActiveIndex >= 0) {
                    updateChartColors(statusChart, statusActiveIndex);
                    statusChart.update('none');
                }

                reservationChart.data.datasets[0].data = [
                    data.reservationStats.success, 
                    data.reservationStats.pending,
                    data.reservationStats.failed
                ];
                resetChartColors(reservationChart);
                reservationChart.update('none');
                if (reservationActiveIndex >= 0) {
                    updateChartColors(reservationChart, reservationActiveIndex);
                    reservationChart.update('none');
                }

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
                
                if (!silent) {
                    document.getElementById('transactionTableBody').style.opacity = '1';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (!silent) {
                    document.getElementById('transactionTableBody').style.opacity = '1';
                }
                alert('Gagal memuat data');
            });
    }

    // Auto-refresh every 3 seconds for near-realtime feel
    setInterval(() => {
        loadData(currentMonth, currentYear, null, true);
    }, 3000);
</script>
@endpush
