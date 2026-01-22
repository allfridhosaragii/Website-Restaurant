@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Kelola Admin</h4>
            <p class="text-muted mb-0">Kelola akses dan izin untuk semua admin</p>
        </div>
        <a href="{{ route('admin.access.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Admin
        </a>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Permissions</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://i.pravatar.cc/40?u={{ $admin->email }}" 
                                         alt="{{ $admin->name }}" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong>{{ $admin->name }}</strong>
                                        @if($admin->isSuperAdmin())
                                        <span class="badge bg-warning text-dark ms-1">Super Admin</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @if($admin->isSuperAdmin())
                                <span class="badge bg-danger">Super Admin</span>
                                @elseif($admin->is_admin)
                                <span class="badge bg-primary">Admin</span>
                                @else
                                <span class="badge bg-info">Viewer</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->status === 'offline')
                                <span class="badge bg-secondary"><i class="bi bi-moon-fill me-1"></i>Offline</span>
                                @elseif($admin->status === 'active' || $admin->status === null)
                                <span class="badge bg-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Online</span>
                                @elseif($admin->status === 'suspended')
                                <span class="badge bg-warning text-dark">Suspended</span>
                                @else
                                <span class="badge bg-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->isSuperAdmin())
                                <span class="text-success"><i class="bi bi-check-circle"></i> Semua Akses</span>
                                @else
                                @php
                                    $enabledCount = $admin->adminPermissions()->where('is_enabled', true)->count();
                                    $totalCount = \App\Models\AdminPermission::getAvailablePermissions();
                                @endphp
                                <span class="text-muted">{{ $enabledCount }}/{{ count($totalCount) }} menu</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->isSuperAdmin())
                                <span class="badge bg-secondary">Super Admin</span>
                                @else
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.access.edit', $admin->id) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit & Permissions
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.access.toggle-super', $admin->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-shield me-2"></i>Toggle Akses Penuh
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.access.toggle-online', $admin->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    @if($admin->status === 'offline')
                                                    <i class="bi bi-toggle-on me-2 text-success"></i>Set Online
                                                    @else
                                                    <i class="bi bi-toggle-off me-2 text-secondary"></i>Set Offline
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.access.destroy', $admin->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">Belum ada admin</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection