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
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0">Status Pesanan</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="statusChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0">Status Pembayaran</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="paymentChart" style="max-height: 250px;"></canvas>
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
</style>
@endpush

@push('scripts')
<script>
    let statusChart = null;
    let paymentChart = null;

    // Scroll to right on load
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('monthTabsContainer');
        container.scrollLeft = container.scrollWidth;
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
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true }
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
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true }
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
