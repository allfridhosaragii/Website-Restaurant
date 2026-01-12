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
        <div class="card">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($orders as $order)
                    <div class="list-group-item p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @php $firstItem = $order->items->first(); @endphp
                                @if($firstItem && $firstItem->image_url)
                                <img src="{{ $firstItem->image_url }}" 
                                     alt="Order" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <div class="rounded-3 me-3 bg-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-bag-check text-white fs-4"></i>
                                </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">#{{ $order->order_number }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>{{ date('d M Y, H:i', strtotime($order->created_at)) }}
                                    </small>
                                    <small class="d-block text-muted">
                                        {{ $order->items->pluck('menu_name')->implode(', ') }}
                                    </small>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($order->status === 'completed')
                                <span class="badge bg-success mb-1">{{ __('messages.status_completed') }}</span>
                                @elseif($order->status === 'processing')
                                <span class="badge bg-warning text-dark mb-1">{{ __('messages.status_processing') }}</span>
                                @elseif($order->status === 'pending')
                                <span class="badge bg-info mb-1">{{ __('messages.status_pending') }}</span>
                                @else
                                <span class="badge bg-danger mb-1">{{ __('messages.status_cancelled') }}</span>
                                @endif
                                <div><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
                            </div>
                        </div>
                        @if($order->status === 'completed')
                        <div class="mt-3">
                            <a href="#" class="btn btn-sm btn-outline-primary me-2">
                                <i class="bi bi-star me-1"></i><span data-i18n="give_rating">{{ __('messages.give_rating') }}</span>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-repeat me-1"></i><span data-i18n="order_again">{{ __('messages.order_again') }}</span>
                            </a>
                        </div>
                        @elseif($order->payment_status === 'pending')
                        <div class="mt-3">
                            <a href="{{ url('/payment/' . $order->id . '/pay') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-credit-card me-1"></i><span data-i18n="pay_now">{{ __('messages.pay_now') }}</span>
                            </a>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="list-group-item p-5 text-center">
                        <i class="bi bi-bag-x fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted mb-2">Belum ada pesanan</h5>
                        <p class="text-muted mb-3">Anda belum pernah melakukan pemesanan. Mulai pesan sekarang!</p>
                        <a href="{{ url('/customer/orders/create') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-bag-plus me-2"></i>Buat Pesanan Pertama
                        </a>
                    </div>
                    @endforelse
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