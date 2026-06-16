@extends('layouts.admin')

@section('title', 'Manajemen Layout Meja')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Layout Meja</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Tambah Layout
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Layout Ruangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Ruangan</th>
                            <th>Grid Ukuran (W x H)</th>
                            <th>Jumlah Meja</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layouts as $layout)
                            <tr>
                                <td>{{ $layout->name }}</td>
                                <td>{{ $layout->grid_width }} x {{ $layout->grid_height }}</td>
                                <td>{{ $layout->tables_count }}</td>
                                <td>
                                    @if($layout->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/admin/table-layouts/' . $layout->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-grid"></i> Desain Layout
                                    </a>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $layout->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ url('/admin/table-layouts/' . $layout->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus layout ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $layout->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ url('/admin/table-layouts/' . $layout->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Layout</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Ruangan</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $layout->name }}" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Grid Width (Kolom)</label>
                                                        <input type="number" class="form-control" name="grid_width" value="{{ $layout->grid_width }}" min="10" max="100" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Grid Height (Baris)</label>
                                                        <input type="number" class="form-control" name="grid_height" value="{{ $layout->grid_height }}" min="10" max="100" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="is_active">
                                                        <option value="1" {{ $layout->is_active ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$layout->is_active ? 'selected' : '' }}>Non-Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada layout</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ url('/admin/table-layouts') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Layout Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" name="name" placeholder="Mis: Lantai 1" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grid Width (Kolom)</label>
                            <input type="number" class="form-control" name="grid_width" value="20" min="10" max="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grid Height (Baris)</label>
                            <input type="number" class="form-control" name="grid_height" value="15" min="10" max="100" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
