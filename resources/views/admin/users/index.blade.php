@extends('layouts.admin')
@section('title', 'Daftar Users')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Daftar Users</h3>
                        <p class="text-muted mb-0">Kelola semua pengguna yang terdaftar</p>
                    </div>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h4 class="mb-0">{{ $totalCount ?? 0 }}</h4>
                    <small class="text-muted">Total Users</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h4 class="mb-0 text-success">{{ $todayCount ?? 0 }}</h4>
                    <small class="text-muted">Registrasi Hari Ini</small>
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
                    <a href="/admin/users?filter=all" 
                       class="btn {{ $filter == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semua
                    </a>
                    <a href="/admin/users?filter=today" 
                       class="btn {{ $filter == 'today' ? 'btn-success' : 'btn-outline-success' }}">
                        Hari Ini
                    </a>
                    <a href="/admin/users?filter=30days" 
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
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Metode Login</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->google_id)
                                            <span class="badge bg-danger"><i class="bi bi-google me-1"></i>Google</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-envelope me-1"></i>Email</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->status == 'blocked')
                                            <span class="badge bg-danger">Blocked</span>
                                        @elseif($user->status == 'suspended')
                                            <span class="badge bg-warning text-dark">Suspended</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Ubah Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form action="/admin/users/{{ $user->id }}/status" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="dropdown-item text-success" {{ $user->status == 'active' ? 'disabled' : '' }}>
                                                            <i class="bi bi-check-circle me-2"></i>Active
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="/admin/users/{{ $user->id }}/status" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="suspended">
                                                        <button type="submit" class="dropdown-item text-warning" {{ $user->status == 'suspended' ? 'disabled' : '' }}>
                                                            <i class="bi bi-exclamation-triangle me-2"></i>Suspend
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="/admin/users/{{ $user->id }}/status" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="blocked">
                                                        <button type="submit" class="dropdown-item text-danger" {{ $user->status == 'blocked' ? 'disabled' : '' }}>
                                                            <i class="bi bi-x-circle me-2"></i>Block
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mb-0">Belum ada user</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="mb-3">Keterangan Status:</h6>
                <div class="row">
                    <div class="col-md-4">
                        <span class="badge bg-success me-2">Active</span>
                        <small class="text-muted">Akun normal, bisa login</small>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-warning text-dark me-2">Suspended</span>
                        <small class="text-muted">Bisa login, muncul peringatan</small>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-danger me-2">Blocked</span>
                        <small class="text-muted">Tidak bisa login sama sekali</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            {{ $users->appends(['filter' => $filter])->links() }}
        </div>
        <div class="mt-4">
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</section>
@endsection