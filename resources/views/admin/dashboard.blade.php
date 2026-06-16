@extends('layouts.admin')
@section('title', __('messages.admin_dashboard'))
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white p-4">
                    <h3 class="text-white mb-2">
                        <i class="bi bi-shield-check me-2"></i><span data-i18n="admin_dashboard">{{ __('messages.admin_dashboard') }}</span>
                    </h3>
                    <p class="opacity-75 mb-0">
                        {{ __('messages.admin_welcome_desc', ['name' => Auth::user()->name]) }}
                    </p>
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
            <div class="col-md-3">
                <div class="card h-100 p-4 border-start border-4 border-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pendapatan Hari Ini</small>
                            <h4 class="mb-0 text-success">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-cash-stack fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 p-4 border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pendapatan Bulan Ini</small>
                            <h4 class="mb-0 text-primary">Rp {{ number_format($monthRevenue ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <i class="bi bi-graph-up-arrow fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 p-4 border-start border-4 border-warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pesanan Pending</small>
                            <h4 class="mb-0 text-warning">{{ $pendingOrders ?? 0 }}</h4>
                        </div>
                        <i class="bi bi-clock-history fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 p-4 border-start border-4 border-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pesanan</small>
                            <h4 class="mb-0 text-info">{{ $totalOrders ?? 0 }}</h4>
                        </div>
                        <i class="bi bi-bag-check fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <a href="/admin/menus" class="card text-decoration-none h-100 p-4 text-center hover-shadow">
                    <i class="bi bi-book-half fs-1 text-primary mb-2"></i>
                    <h6 data-i18n="manage_menus">{{ __('messages.manage_menus') }}</h6>
                </a>
            </div>
            <div class="col-md-3">
                <a href="/admin/orders" class="card text-decoration-none h-100 p-4 text-center hover-shadow">
                    <i class="bi bi-bag fs-1 text-success mb-2"></i>
                    <h6>Kelola Pesanan</h6>
                    @if(($pendingOrders ?? 0) > 0)
                    <span class="badge bg-danger">{{ $pendingOrders }} pending</span>
                    @endif
                </a>
            </div>
            <div class="col-md-3">
                <a href="/admin/reservations" class="card text-decoration-none h-100 p-4 text-center hover-shadow">
                    <i class="bi bi-calendar-check fs-1 text-info mb-2"></i>
                    <h6 data-i18n="manage_reservations">{{ __('messages.manage_reservations') }}</h6>
                </a>
            </div>
            <div class="col-md-3">
                <a href="/admin/users" class="card text-decoration-none h-100 p-4 text-center hover-shadow">
                    <i class="bi bi-people fs-1 text-warning mb-2"></i>
                    <h6 data-i18n="user_list">{{ __('messages.user_list') }}</h6>
                </a>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-bag text-primary me-2"></i>Pesanan Terbaru
                        </h5>
                        <a href="/admin/orders" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentOrders) && count($recentOrders) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentOrders as $order)
                                    <a href="/admin/orders/{{ $order->id }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>#{{ $order->order_number }}</strong>
                                                <small class="text-muted d-block">{{ $order->customer_name }}</small>
                                            </div>
                                            <div class="text-end">
                                                @if($order->status === 'completed')
                                                <span class="badge bg-success">Selesai</span>
                                                @elseif($order->status === 'processing')
                                                <span class="badge bg-info">Diproses</span>
                                                @elseif($order->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                <span class="badge bg-danger">Batal</span>
                                                @endif
                                                <small class="d-block text-muted">Rp {{ number_format($order->total, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0">Belum ada pesanan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-activity text-primary me-2"></i><span data-i18n="recent_activities">{{ __('messages.recent_activities') }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($recentActivities) && $recentActivities->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentActivities as $activity)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $activity->user_name ?? 'Guest' }}</strong>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                                        </div>
                                        <small class="text-muted">{{ $activity->action }} - {{ $activity->description }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0" data-i18n="no_activities">{{ __('messages.no_activities') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection