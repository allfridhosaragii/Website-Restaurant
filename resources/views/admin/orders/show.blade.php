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

    @if(isset($tableGroup) && $tableGroup)
    <div class="alert alert-info shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-diagram-3-fill fs-4 me-2"></i>
                <strong>Tergabung dalam {{ $tableGroup->name }}</strong>
                <span class="ms-2 badge bg-primary">Grup</span>
            </div>
            <form action="{{ url('/admin/orders/' . $order->id . '/unmerge') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin melepas meja ini dari grup?')">
                    <i class="bi bi-node-minus me-1"></i>Lepas dari Grup
                </button>
            </form>
        </div>
        
        @if(isset($groupOrders) && count($groupOrders) > 0)
        <hr>
        <div class="small">
            <span class="text-muted">Order lain di grup ini:</span>
            <div class="d-flex gap-2 mt-2 flex-wrap">
                @foreach($groupOrders as $gOrder)
                    <a href="{{ url('/admin/orders/' . $gOrder->id) }}" class="btn btn-sm btn-light border">
                        Meja {{ $gOrder->table_number }} (#{{ $gOrder->order_number }})
                    </a>
                @endforeach
            </div>
        </div>
        @endif
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
                        @if(isset($hasPartialRefund) && $hasPartialRefund)
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2 ms-2">Partially Refunded</span>
                        @endif
                        @elseif($order->status === 'processing')
                        <span class="badge bg-info fs-6 px-3 py-2">Diproses</span>
                        @elseif($order->status === 'pending')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Menunggu</span>
                        @elseif($order->status === 'refunded')
                        <span class="badge bg-secondary fs-6 px-3 py-2">Direfund</span>
                        @else
                        <span class="badge bg-danger fs-6 px-3 py-2">Dibatalkan</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($order->items as $item)
                        <div class="list-group-item p-4 {{ isset($item->status) && $item->status === 'voided' ? 'bg-light' : '' }}">
                            <div class="d-flex align-items-center">
                                @if($item->image_url)
                                <img src="{{ $item->image_url }}" 
                                     alt="{{ $item->menu_name }}" 
                                     class="rounded-3 me-3 {{ isset($item->status) && $item->status === 'voided' ? 'opacity-50' : '' }}" 
                                     style="width: 70px; height: 70px; object-fit: cover;">
                                @else
                                <div class="rounded-3 me-3 bg-secondary d-flex align-items-center justify-content-center {{ isset($item->status) && $item->status === 'voided' ? 'opacity-50' : '' }}" 
                                     style="width: 70px; height: 70px;">
                                    <i class="bi bi-image text-white fs-4"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1 {{ isset($item->status) && $item->status === 'voided' ? 'text-decoration-line-through text-muted' : '' }}">
                                    <h6 class="mb-1">
                                        {{ $item->menu_name }}
                                        @if(isset($item->is_promo) && $item->is_promo)
                                            <span class="badge bg-success ms-2 text-xs">Promo: {{ $item->promo_name }}</span>
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
                                    @if(isset($item->status) && $item->status === 'voided')
                                    <div class="mt-1">
                                        <span class="badge bg-danger">VOIDED</span>
                                        <small class="text-danger d-block mt-1">Alasan: {{ $item->void_reason }}</small>
                                    </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <strong class="{{ isset($item->status) && $item->status === 'voided' ? 'text-decoration-line-through text-muted' : '' }}">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </strong>
                                    
                                    @if((!isset($item->status) || $item->status === 'active') && !in_array($order->status, ['completed', 'cancelled']))
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#voidModal{{ $item->id }}">
                                            <i class="bi bi-x-circle"></i> Void
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal Void Item -->
                        @if((!isset($item->status) || $item->status === 'active') && !in_array($order->status, ['completed', 'cancelled']))
                        <div class="modal fade" id="voidModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Void Item: {{ $item->menu_name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('/admin/orders/' . $order->id . '/items/' . $item->id . '/void') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Tindakan ini membutuhkan otorisasi Manager/Supervisor.
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Void <span class="text-danger">*</span></label>
                                                <input type="text" name="void_reason" class="form-control" required placeholder="Contoh: Pelanggan batal, stok habis, dll">
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="form-label">Email Manager <span class="text-danger">*</span></label>
                                                <input type="email" name="manager_email" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password Manager <span class="text-danger">*</span></label>
                                                <input type="password" name="manager_password" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Void Item</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($order->subtotal_before_discount ?? $order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
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
                        <form action="{{ url('/admin/orders/' . $order->id . '/mark-paid') }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Tandai pesanan ini sebagai LUNAS?')">
                                <i class="bi bi-check-circle me-2"></i>Tandai Lunas (Manual)
                            </button>
                        </form>
                        
                        @if($order->status !== 'cancelled')
                        <a href="{{ url('/admin/orders/' . $order->id . '/split') }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-layout-split me-2"></i>Split Bill
                        </a>
                        @endif
                    </div>
                    @endif
                    
                    <div class="mt-2">
                        <a href="{{ url('/admin/orders/' . $order->id . '/kitchen-print') }}" target="_blank" class="btn btn-outline-dark w-100 mb-2">
                            <i class="bi bi-printer me-2"></i>Print ke Dapur
                        </a>
                    </div>
                    
                    @if(in_array($order->status, ['pending', 'processing']) && $order->table_id)
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#moveTableModal">
                            <i class="bi bi-arrow-left-right me-2"></i>Pindah Meja
                        </button>
                    </div>
                    
                    <!-- Move Table Modal -->
                    <div class="modal fade" id="moveTableModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ url('/admin/orders/' . $order->id . '/move-table') }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Pindah Meja</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            Meja saat ini: <strong>{{ $order->table_number }}</strong>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Meja Tujuan (Tersedia)</label>
                                            <select name="to_table_id" class="form-select" required>
                                                <option value="">-- Pilih Meja --</option>
                                                @foreach($availableTables as $table)
                                                    <option value="{{ $table->id }}">Meja {{ $table->number }} (Kapasitas: {{ $table->capacity }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Alasan Pindah (Opsional)</label>
                                            <textarea name="reason" class="form-control" rows="2" placeholder="Misal: Meja sebelumnya panas..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin memindah pesanan ini?')">Konfirmasi Pindah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($order->status !== 'completed' && $order->status !== 'cancelled')
            <div class="card mb-4">
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

            @if($order->status === 'completed')
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-counterclockwise me-2 text-danger"></i>Pengembalian Dana</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Ajukan refund sebagian atau penuh untuk pesanan ini.</p>
                    <a href="{{ url('/admin/orders/' . $order->id . '/refund') }}" class="btn btn-outline-danger w-100">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Ajukan Refund
                    </a>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-printer me-2 text-primary"></i>Cetak & Bagikan</h6>
                </div>
                <div class="card-body text-center">
                    @if(in_array($order->status, ['completed', 'refunded']))
                        <div class="d-grid gap-2">
                            <a href="#" onclick="window.open('{{ url('/admin/orders/' . $order->id . '/receipt/print') }}', 'Cetak Struk', 'width=400,height=600'); return false;" class="btn btn-primary">
                                <i class="bi bi-printer me-2"></i>Print Struk
                            </a>
                            <a href="{{ url('/admin/orders/' . $order->id . '/receipt/whatsapp') }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp me-2"></i>Kirim WA
                            </a>
                            <a href="{{ url('/admin/orders/' . $order->id . '/receipt/email') }}" class="btn btn-danger">
                                <i class="bi bi-envelope me-2"></i>Kirim Email
                            </a>
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <button class="btn btn-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                <i class="bi bi-printer me-2"></i>Print Struk
                            </button>
                            <button class="btn btn-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                <i class="bi bi-whatsapp me-2"></i>Kirim WA
                            </button>
                            <button class="btn btn-secondary disabled" title="Selesaikan pembayaran terlebih dahulu">
                                <i class="bi bi-envelope me-2"></i>Kirim Email
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">Hanya untuk pesanan selesai</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection