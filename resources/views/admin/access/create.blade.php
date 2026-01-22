@extends('layouts.admin')
@section('title', 'Tambah Admin')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Tambah Admin Baru</h4>
            <p class="text-muted mb-0">Buat akun admin baru</p>
        </div>
        <a href="{{ route('admin.access.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.access.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe Admin</label>
                        <select name="admin_type" class="form-select @error('admin_type') is-invalid @enderror" required>
                            <option value="admin" {{ old('admin_type') == 'admin' ? 'selected' : '' }}>Admin Biasa</option>
                            <option value="super_admin" {{ old('admin_type') == 'super_admin' ? 'selected' : '' }}>Admin dengan Akses Penuh</option>
                        </select>
                        @error('admin_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Admin Biasa = dapat diatur menu yang tersedia</small>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Buat Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection