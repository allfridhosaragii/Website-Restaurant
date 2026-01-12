@extends('layouts.admin')
@section('title', 'Detail Pesanan #' . $order->order_number)
@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ url('/admin/orders') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Pesanan #{{ $order->order_number }}</h5>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>{{ date('d M Y, H:i', strtotime($order->created_at)) }}
                        </small>
                    </div>
                    <div>
                        @if($order->status === 'completed')
                        <span class="badge bg-success fs-6 px-3 py-2">Selesai</span>
                        @elseif($order->status === 'processing')
                        <span class="badge bg-info fs-6 px-3 py-2">Diproses</span>
                        @elseif($order->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Menunggu</span>
                        @else
                        <span class="badge bg-danger fs-6 px-3 py-2">Dibatalkan</span>
                        @endif
                    </div>
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
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pajak (10%)</span>
                                <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-primary fs-5">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($order->notes)
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-left-text me-2 text-primary"></i>Catatan Pelanggan</h6>
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
                    <h6 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>Info Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <span class="text-white fs-5">{{ strtoupper(substr($order->customer_name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->customer_name }}</h6>
                            <small class="text-muted">{{ $order->customer_email }}</small>
                        </div>
                    </div>
                    @if($order->customer_phone)
                    <div class="mb-2">
                        <small class="text-muted">Telepon</small>
                        <div>{{ $order->customer_phone }}</div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Detail Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Tipe Pesanan</small>
                        <div>
                            @if($order->type === 'dine_in')
                            <span class="badge bg-info">Dine In</span>
                            @else
                            <span class="badge bg-secondary">Take Away</span>
                            @endif
                        </div>
                    </div>
                    @if($order->table_number)
                    <div class="mb-3">
                        <small class="text-muted">Nomor Meja</small>
                        <div class="fs-5 fw-bold">{{ $order->table_number }}</div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted">Status Pembayaran</small>
                        <div>
                            @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Lunas</span>
                            @elseif($order->payment_status === 'pending')
                            <span class="badge bg-warning text-dark">Belum Bayar</span>
                            @else
                            <span class="badge bg-danger">Gagal</span>
                            @endif
                        </div>
                    </div>
                    @if($order->payment_status === 'pending')
                    <div class="mt-3">
                        <form action="{{ url('/admin/orders/' . $order->id . '/mark-paid') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Tandai pesanan ini sebagai LUNAS?')">
                                <i class="bi bi-check-circle me-2"></i>Tandai Lunas (Manual)
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @if($order->status !== 'completed' && $order->status !== 'cancelled')
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-gear me-2 text-primary"></i>Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/orders/' . $order->id . '/status') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Batalkan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection