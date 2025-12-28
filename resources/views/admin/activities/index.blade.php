@extends('layouts.admin')
@section('title', 'Log Aktivitas')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Log Aktivitas</h3>
                        <p class="text-muted mb-0">Lihat semua aktivitas pengguna</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h4 class="mb-0">{{ $totalCount ?? 0 }}</h4>
                    <small class="text-muted">Total Aktivitas</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-success">{{ $todayCount ?? 0 }}</h4>
                    <small class="text-muted">Hari Ini</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-info">{{ $last30DaysCount ?? 0 }}</h4>
                    <small class="text-muted">30 Hari Terakhir</small>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="/admin/activities?filter=all" 
                       class="btn {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semua
                    </a>
                    <a href="/admin/activities?filter=today" 
                       class="btn {{ $filter == 'today' ? 'btn-success' : 'btn-outline-success' }}">
                        Hari Ini
                    </a>
                    <a href="/admin/activities?filter=30days" 
                       class="btn {{ $filter == '30days' ? 'btn-info' : 'btn-outline-info' }}">
                        30 Hari Terakhir
                    </a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($activity->created_at)->format('d M Y') }}</small>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->user_name ?? 'Guest' }}</strong>
                                        @if($activity->user_email)
                                            <br><small class="text-muted">{{ $activity->user_email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->action == 'login')
                                            <span class="badge bg-success">Login</span>
                                        @elseif($activity->action == 'logout')
                                            <span class="badge bg-secondary">Logout</span>
                                        @else
                                            <span class="badge bg-primary">{{ $activity->action }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $activity->description ?? '-' }}</td>
                                    <td><code>{{ $activity->ip_address }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mb-0">Belum ada aktivitas</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4">
            {{ $activities->appends(['filter' => $filter])->links() }}
        </div>
        <div class="mt-4">
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</section>
@endsection