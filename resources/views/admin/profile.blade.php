@extends('layouts.admin')
@section('title', 'Profil Saya')
@section('content')
<div class="row">
    <div class="col-12 px-4 px-lg-5 pt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold fs-3 mb-1">Profil Saya</h2>
                <p class="text-muted mb-0">Kelola informasi akun Anda</p>
            </div>
        </div>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white p-4 border-bottom">
                        <h5 class="mb-0 fw-bold">Informasi Akun</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ url('/admin/profile') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="name" class="form-label text-muted small text-uppercase fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label text-muted small text-uppercase fw-bold">Email Address</label>
                                <input type="email" class="form-control form-control-lg bg-light border-0" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr class="my-5">
                            <h5 class="fw-bold mb-4">Ganti Password</h5>
                            <p class="text-muted mb-4 small">Biarkan kosong jika tidak ingin mengubah password.</p>
                            <div class="mb-4">
                                <label for="current_password" class="form-label text-muted small text-uppercase fw-bold">Password Saat Ini</label>
                                <input type="password" class="form-control form-control-lg bg-light border-0" id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="password" class="form-label text-muted small text-uppercase fw-bold">Password Baru</label>
                                    <input type="password" class="form-control form-control-lg bg-light border-0" id="password" name="password">
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label text-muted small text-uppercase fw-bold">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control form-control-lg bg-light border-0" id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <button type="reset" class="btn btn-light px-4 py-2 rounded-3">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-primary text-white mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 overflow-hidden" 
                             style="width: 80px; height: 80px; background-color: rgba(255, 255, 255, 0.25);">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" 
                                 class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <p class="opacity-75 mb-0">{{ $user->email }}</p>
                        <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                            <small class="text-uppercase opacity-75 d-block mb-1" style="font-size: 0.7rem;">Role</small>
                            <span class="fw-bold"><i class="bi bi-shield-check me-1"></i> Administrator</span>
                        </div>
                        <div class="mt-3">
                            <small class="text-uppercase opacity-75 d-block mb-1" style="font-size: 0.7rem;">Bergabung Sejak</small>
                            <span class="fw-bold">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection