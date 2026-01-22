@extends('layouts.guest')
@section('title', __('messages.my_orders'))
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1" data-i18n="my_orders">{{ __('messages.my_orders') }}</h3>
                        <p class="text-muted mb-0" data-i18n="my_orders_desc">{{ __('messages.my_orders_desc') }}</p>
                    </div>
                    <a href="{{ url('/customer/orders/create') }}" class="btn btn-primary">
                        <i class="bi bi-bag-plus me-2"></i><span data-i18n="create_order_btn">{{ __('messages.create_order_btn') }}</span>
                    </a>
                </div>
            </div>
        </div>
        @php
            $pendingOrders = $orders->filter(function($order) {
                return $order->payment_status === 'pending' && $order->status !== 'cancelled';
            });
            $historyOrders = $orders->filter(function($order) {
                return $order->payment_status !== 'pending' || $order->status === 'cancelled';
            });
        @endphp
        <div class="card bg-transparent shadow-none border-0">
            <div class="card-header bg-transparent border-0 p-0 mb-3">
                <ul class="nav nav-pills gap-2" id="orderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                            <i class="bi bi-hourglass-split me-2"></i>Menunggu Pembayaran
                            @if($pendingOrders->count() > 0)
                            <span class="badge bg-white text-primary ms-2 rounded-pill">{{ $pendingOrders->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Pesanan
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="orderTabsContent">
                    <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                        <div class="list-group rounded-3 shadow-sm">
                            @forelse($pendingOrders as $order)
                            <div class="list-group-item p-4 border-start border-5 border-warning">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            @php $firstItem = $order->items->first(); @endphp
                                            @if($firstItem && $firstItem->image_url)
                                            <img src="{{ $firstItem->image_url }}" alt="Order" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                            <div class="rounded-3 me-3 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="mb-0">#{{ $order->order_number }}</h6>
                                                    <span class="badge bg-warning text-dark">{{ __('messages.status_pending') }}</span>
                                                </div>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="bi bi-calendar me-1"></i>{{ date('d M Y, H:i', strtotime($order->created_at)) }}
                                                </small>
                                                <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                                    {{ $order->items->pluck('menu_name')->implode(', ') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="mb-2 text-muted small">Total Pembayaran</div>
                                        <h5 class="text-primary fw-bold mb-3">Rp {{ number_format($order->total, 0, ',', '.') }}</h5>
                                        <a href="{{ url('/customer/payment/' . $order->id . '/pay') }}" class="btn btn-primary w-100">
                                            <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 bg-white rounded-3">
                                <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
                                <h5 class="text-muted mb-2">Tidak ada tagihan</h5>
                                <p class="text-muted mb-0">Semua pesanan Anda sudah dibayar!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <div class="list-group rounded-3 shadow-sm">
                            @forelse($historyOrders as $order)
                            <div class="list-group-item p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @php $firstItem = $order->items->first(); @endphp
                                        @if($firstItem && $firstItem->image_url)
                                        <img src="{{ $firstItem->image_url }}" alt="Order" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                        <div class="rounded-3 me-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-bag-check text-secondary fs-4"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h6 class="mb-0">#{{ $order->order_number }}</h6>
                                                @if($order->status === 'completed')
                                                <span class="badge bg-success">{{ __('messages.status_completed') }}</span>
                                                @elseif($order->status === 'processing')
                                                <span class="badge bg-info">{{ __('messages.status_processing') }}</span>
                                                @else
                                                <span class="badge bg-danger">{{ __('messages.status_cancelled') }}</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-calendar me-1"></i>{{ date('d M Y, H:i', strtotime($order->created_at)) }}
                                            </small>
                                            <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                                {{ $order->items->pluck('menu_name')->implode(', ') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1 fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                        @if($order->status === 'completed')
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-repeat me-1"></i>Pesan Lagi
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 bg-white rounded-3">
                                <i class="bi bi-clock-history fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted mb-2">Belum ada riwayat</h5>
                                <p class="text-muted mb-3">Riwayat pesanan Anda akan muncul di sini.</p>
                                <a href="{{ url('/customer/orders/create') }}" class="btn btn-primary">
                                    <i class="bi bi-bag-plus me-2"></i>Buat Pesanan Baru
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ url('/customer/dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i><span data-i18n="back_to_dashboard">{{ __('messages.back_to_dashboard') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection