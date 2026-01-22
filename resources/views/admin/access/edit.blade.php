@extends('layouts.admin')
@section('title', 'Edit Admin')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Admin: {{ $admin->name }}</h4>
            <p class="text-muted mb-0">Perbarui informasi dan izin menu</p>
        </div>
        <a href="{{ route('admin.access.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <form action="{{ route('admin.access.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Informasi Admin</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $admin->email) }}" required
                                   {{ $admin->isSuperAdmin() ? 'readonly' : '' }}>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru (opsional)</label>
                            <input type="password" name="password" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Izin Menu Sidebar</h6>
                    </div>
                    <div class="card-body">
                        @if($admin->isSuperAdmin())
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>Super Admin memiliki akses ke semua menu.
                        </div>
                        @else
                        <p class="text-muted mb-3">Pilih menu yang dapat diakses oleh admin ini:</p>
                        <div class="row">
                            @foreach($permissions as $key => $label)
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           name="permissions[]" 
                                           value="{{ $key }}" 
                                           id="perm_{{ $key }}"
                                           {{ (isset($userPermissions[$key]) && $userPermissions[$key]) || !isset($userPermissions[$key]) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $key }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAll()">
                                <i class="bi bi-check-all"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAll()">
                                <i class="bi bi-x"></i> Hapus Semua
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
<script>
function checkAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
}
function uncheckAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
}
</script>
@endsection