@extends('layouts.guest')
@section('title', 'Detail Pesanan #' . $order->order_number)
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ url('/customer/orders') }}" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Pesanan
                </a>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Pesanan #{{ $order->order_number }}</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar me-1"></i>{{ date('d M Y, H:i', strtotime($order->created_at)) }}
                        </p>
                    </div>
                    <div>
                        @if($order->status === 'completed')
                        <span class="badge bg-success fs-6 px-3 py-2">Selesai</span>
                        @elseif($order->status === 'processing')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Diproses</span>
                        @elseif($order->status === 'pending')
                        <span class="badge bg-info fs-6 px-3 py-2">Menunggu</span>
                        @else
                        <span class="badge bg-danger fs-6 px-3 py-2">Dibatalkan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bag-check me-2 text-primary"></i>Item Pesanan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($order->items as $item)
                            <div class="list-group-item p-4">
                                <div class="d-flex align-items-center">
                                    @if($item->image_url)
                                    <img src="{{ $item->image_url }}" 
                                         alt="{{ $item->menu_name }}" 
                                         class="rounded-3 me-3" 
                                         style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                    <div class="rounded-3 me-3 bg-secondary d-flex align-items-center justify-content-center" 
                                         style="width: 70px; height: 70px;">
                                        <i class="bi bi-image text-white fs-4"></i>
                                    </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item->menu_name }}</h6>
                                        <small class="text-muted">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text me-2 text-primary"></i>Catatan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tipe Pesanan</span>
                            <strong>{{ $order->type === 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang' }}</strong>
                        </div>
                        @if($order->table_number)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Nomor Meja</span>
                            <strong>{{ $order->table_number }}</strong>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak (10%)</span>
                            <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-primary fs-5">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Status</span>
                            @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Lunas</span>
                            @elseif($order->payment_status === 'pending')
                            <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </div>
                        @if($order->payment_status === 'pending')
                        <a href="{{ url('/customer/payment/' . $order->id . '/pay') }}" class="btn btn-primary w-100">
                            <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection