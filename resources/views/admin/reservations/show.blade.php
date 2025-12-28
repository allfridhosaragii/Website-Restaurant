@extends('layouts.admin')
@section('title', 'Detail Reservasi')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="/admin/reservations" class="btn btn-outline-primary mb-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
                <h3 class="mb-1">Detail Reservasi #{{ $reservation->id }}</h3>
                <p class="text-muted mb-0">Lihat detail dan kelola status reservasi</p>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Reservasi</h5>
                        @if($reservation->status === 'pending')
                            <span class="badge bg-warning text-dark fs-6">Menunggu Konfirmasi</span>
                        @elseif($reservation->status === 'accepted')
                            <span class="badge bg-success fs-6">Diterima</span>
                        @else
                            <span class="badge bg-danger fs-6">Ditolak</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nama Pelanggan</label>
                                <p class="fw-bold mb-0">{{ $reservation->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <p class="fw-bold mb-0">{{ $reservation->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Telepon</label>
                                <p class="fw-bold mb-0">{{ $reservation->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Jumlah Tamu</label>
                                <p class="fw-bold mb-0">{{ $reservation->guests }} orang</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Tanggal Reservasi</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-calendar-event text-gold me-1"></i>
                                    {{ \Carbon\Carbon::parse($reservation->date)->format('l, d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Waktu</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-clock text-gold me-1"></i>
                                    {{ $reservation->time }}
                                </p>
                            </div>
                            @if($reservation->table_id)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Meja</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-grid-3x3 text-gold me-1"></i>
                                    Meja {{ $reservation->table_id }}
                                </p>
                            </div>
                            @endif
                            @if($reservation->notes)
                            <div class="col-12">
                                <label class="form-label text-muted small">Permintaan Khusus</label>
                                <p class="mb-0">{{ $reservation->notes }}</p>
                            </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label text-muted small">Dibuat Pada</label>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Perbarui Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="/admin/reservations/{{ $reservation->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ $reservation->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ $reservation->status === 'accepted' ? 'selected' : '' }}>Diterima</option>
                                    <option value="rejected" {{ $reservation->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="admin_notes" class="form-label">Catatan Admin</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                          placeholder="Catatan untuk pelanggan (opsional)">{{ $reservation->admin_notes }}</textarea>
                                <small class="text-muted">Catatan ini akan ditampilkan ke pelanggan.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Bukti Transfer</h5>
                    </div>
                    <div class="card-body">
                        @if($reservation->payment_proof)
                            <a href="{{ $reservation->payment_proof }}" target="_blank">
                                <img src="{{ $reservation->payment_proof }}" 
                                     alt="Bukti Transfer" 
                                     class="img-fluid rounded mb-3"
                                     style="cursor: zoom-in;">
                            </a>
                            <div class="d-grid">
                                <a href="{{ $reservation->payment_proof }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>Buka Ukuran Penuh
                                </a>
                            </div>
                            <p class="text-muted small text-center mt-2 mb-0">
                                Klik gambar untuk melihat ukuran penuh tanpa kompresi
                            </p>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-image display-4 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada bukti transfer</p>
                            </div>
                        @endif
                    </div>
                </div>
                @if($reservation->status === 'pending')
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <form action="/admin/reservations/{{ $reservation->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg me-2"></i>Terima Reservasi
                            </button>
                        </form>
                        <form action="/admin/reservations/{{ $reservation->id }}/status" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-lg me-2"></i>Tolak Reservasi
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection