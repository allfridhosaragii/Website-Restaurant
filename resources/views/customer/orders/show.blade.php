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
                                        <h6 class="mb-1">
                                            {{ $item->menu_name }}
                                            @if(isset($item->is_promo) && $item->is_promo)
                                                <span class="badge bg-success ms-2" style="font-size: 0.65rem;">Promo: {{ $item->promo_name }}</span>
                                            @endif
                                        </h6>
                                        @php $mods = $item->modifiers ? json_decode($item->modifiers, true) : null; @endphp
                                        @if($mods && is_array($mods))
                                            <div class="small text-muted mb-1">
                                                @foreach($mods as $mod)
                                                    <div>- {{ $mod['name'] }}: {{ $mod['option_name'] }} {!! $mod['price'] > 0 ? '(+Rp '.number_format($mod['price'], 0, ',', '.').')' : '' !!}</div>
                                                @endforeach
                                            </div>
                                        @endif
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
                            <span>Rp {{ number_format($order->subtotal_before_discount ?? $order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span class="text-muted text-danger">Diskon</span>
                            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
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

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-printer me-2 text-primary"></i>Cetak & Bagikan</h5>
                    </div>
                    <div class="card-body text-center">
                        @if(in_array($order->status, ['completed', 'refunded']))
                            <div class="d-grid gap-2">
                                <a href="#" onclick="window.open('{{ url('/customer/orders/' . $order->id . '/receipt/print') }}', 'Cetak Struk', 'width=400,height=600'); return false;" class="btn btn-outline-primary">
                                    <i class="bi bi-printer me-2"></i>Print Struk
                                </a>
                                <a href="{{ url('/customer/orders/' . $order->id . '/receipt/whatsapp') }}" target="_blank" class="btn btn-outline-success">
                                    <i class="bi bi-whatsapp me-2"></i>Kirim ke WA Saya
                                </a>
                                <a href="{{ url('/customer/orders/' . $order->id . '/receipt/email') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-envelope me-2"></i>Kirim ke Email Saya
                                </a>
                            </div>
                        @else
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                    <i class="bi bi-printer me-2"></i>Print Struk
                                </button>
                                <button class="btn btn-outline-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                    <i class="bi bi-whatsapp me-2"></i>Kirim WA
                                </button>
                                <button class="btn btn-outline-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                    <i class="bi bi-envelope me-2"></i>Kirim Email
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block">Hanya tersedia jika sudah lunas</small>
                        @endif
                    </div>
                </div>

                @if($order->status == 'completed')
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-star me-2 text-warning"></i>Rating & Review</h5>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $review = $order->reviews()->whereNull('menu_id')->first();
                        @endphp
                        
                        @if($review)
                            <div class="text-warning mb-2 fs-4">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            @if($review->comment)
                                <p class="fst-italic text-muted">"{{ $review->comment }}"</p>
                            @endif
                            @if($review->admin_reply)
                                <div class="bg-light p-3 rounded-3 mt-3 text-start">
                                    <small class="text-primary fw-bold"><i class="bi bi-reply-fill"></i> Balasan Admin:</small>
                                    <p class="mb-0 small mt-1">{{ $review->admin_reply }}</p>
                                </div>
                            @endif
                            <small class="text-success d-block mt-3"><i class="bi bi-check-circle"></i> Anda sudah memberikan review</small>
                        @else
                            <p class="text-muted mb-3">Bagaimana pengalaman Anda dengan pesanan ini?</p>
                            <button type="button" class="btn btn-warning w-100 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="bi bi-star-fill me-2"></i>Berikan Review
                            </button>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

<!-- Review Modal -->
@if($order->status == 'completed' && !$order->reviews()->whereNull('menu_id')->exists())
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Berikan Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customer.orders.review', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body text-center pt-2">
                    <p class="text-muted mb-4">Pesanan #{{ $order->order_number }}</p>
                    
                    <div class="rating-css mb-4">
                        <div class="star-icon text-warning fs-1" style="display: flex; flex-direction: row-reverse; justify-content: center;">
                            <input type="radio" name="rating" value="5" id="rating5" class="d-none" required>
                            <label for="rating5" class="bi bi-star" style="cursor:pointer; padding: 0 5px;"></label>
                            
                            <input type="radio" name="rating" value="4" id="rating4" class="d-none">
                            <label for="rating4" class="bi bi-star" style="cursor:pointer; padding: 0 5px;"></label>
                            
                            <input type="radio" name="rating" value="3" id="rating3" class="d-none">
                            <label for="rating3" class="bi bi-star" style="cursor:pointer; padding: 0 5px;"></label>
                            
                            <input type="radio" name="rating" value="2" id="rating2" class="d-none">
                            <label for="rating2" class="bi bi-star" style="cursor:pointer; padding: 0 5px;"></label>
                            
                            <input type="radio" name="rating" value="1" id="rating1" class="d-none">
                            <label for="rating1" class="bi bi-star" style="cursor:pointer; padding: 0 5px;"></label>
                        </div>
                    </div>
                    
                    <div class="text-start">
                        <label class="form-label">Komentar (Opsional)</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Bagikan pengalaman Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Nanti Saja</button>
                    <button type="submit" class="btn btn-primary px-4">Kirim Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* CSS for simple star rating hover effect */
    .rating-css .star-icon label:hover,
    .rating-css .star-icon label:hover ~ label,
    .rating-css .star-icon input:checked ~ label {
        color: #ffc107 !important;
    }
    .rating-css .star-icon label::before {
        content: "\f586"; /* bi-star */
    }
    .rating-css .star-icon label:hover::before,
    .rating-css .star-icon label:hover ~ label::before,
    .rating-css .star-icon input:checked ~ label::before {
        content: "\f588"; /* bi-star-fill */
    }
</style>
@endif

@endsection