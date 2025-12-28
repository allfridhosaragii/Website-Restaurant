@extends('layouts.admin')
@section('title', 'Manajemen Reservasi')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-1">Manajemen Reservasi</h3>
                <p class="text-muted mb-0">Kelola permintaan reservasi dari pelanggan</p>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-warning bg-opacity-10 border-warning">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-warning">{{ $pendingCount }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 border-success">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-success">{{ $acceptedCount }}</h3>
                        <small class="text-muted">Diterima</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger bg-opacity-10 border-danger">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-danger">{{ $rejectedCount }}</h3>
                        <small class="text-muted">Ditolak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary bg-opacity-10 border-primary">
                    <div class="card-body text-center">
                        <h3 class="mb-0 text-primary">{{ $todayCount }}</h3>
                        <small class="text-muted">Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="/admin/reservations?filter=all" 
               class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                Semua
            </a>
            <a href="/admin/reservations?filter=pending" 
               class="btn {{ $filter === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                <i class="bi bi-clock me-1"></i>Pending ({{ $pendingCount }})
            </a>
            <a href="/admin/reservations?filter=accepted" 
               class="btn {{ $filter === 'accepted' ? 'btn-success' : 'btn-outline-success' }}">
                <i class="bi bi-check-circle me-1"></i>Diterima
            </a>
            <a href="/admin/reservations?filter=rejected" 
               class="btn {{ $filter === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="bi bi-x-circle me-1"></i>Ditolak
            </a>
            <a href="/admin/reservations?filter=today" 
               class="btn {{ $filter === 'today' ? 'btn-info' : 'btn-outline-info' }}">
                <i class="bi bi-calendar-day me-1"></i>Hari Ini
            </a>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Tamu</th>
                            <th>Bukti TF</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                        <tr>
                            <td><strong>#{{ $reservation->id }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $reservation->name }}</strong>
                                    <br><small class="text-muted">{{ $reservation->email }}</small>
                                </div>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($reservation->date)->format('d M Y') }}</strong>
                                <br><small class="text-muted">{{ $reservation->time }}</small>
                            </td>
                            <td>{{ $reservation->guests }} orang</td>
                            <td>
                                @if($reservation->payment_proof)
                                    <a href="{{ $reservation->payment_proof }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-image"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($reservation->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($reservation->status === 'accepted')
                                    <span class="badge bg-success">Diterima</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/admin/reservations/{{ $reservation->id }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($reservation->status === 'pending')
                                        <form action="/admin/reservations/{{ $reservation->id }}/status" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="btn btn-sm btn-success" title="Terima">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="/admin/reservations/{{ $reservation->id }}/status" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-calendar-x display-4 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada reservasi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $reservations->appends(['filter' => $filter])->links() }}
        </div>
    </div>
</section>
@endsection