@extends('layouts.admin')

@section('title', 'Manajemen Pengeluaran')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengeluaran</h1>
        <div>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="bi bi-tags"></i> Kategori
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-lg"></i> Tambah Pengeluaran
            </button>
        </div>
    </div>

    <!-- Filter & Summary -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow h-100">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="col-form-label">Bulan/Tahun</label>
                        </div>
                        <div class="col-auto">
                            <input type="month" name="month" class="form-control" value="{{ request('month', date('Y-m')) }}">
                        </div>
                        <div class="col-auto">
                            <label class="col-form-label">Kategori</label>
                        </div>
                        <div class="col-auto">
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 text-uppercase mb-2">Total Pengeluaran</h6>
                    <h3 class="mb-0 fw-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Judul/Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Pencatat</th>
                            <th>Nota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                        <tr>
                            <td>{{ $exp->date->format('d M Y') }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $exp->category->color }}">
                                    {{ $exp->category->name }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $exp->title }}</strong><br>
                                <small class="text-muted">{{ $exp->description }}</small>
                            </td>
                            <td class="fw-bold text-danger">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td>{{ $exp->creator->name }}</td>
                            <td>
                                @if($exp->receipt_image)
                                <a href="{{ Storage::url($exp->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-image"></i> Lihat</a>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ url('/admin/expenses/'.$exp->id) }}" method="POST" onsubmit="return confirm('Hapus data pengeluaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data pengeluaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ url('/admin/expenses') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catat Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Pengeluaran</label>
                        <input type="text" class="form-control" name="title" required placeholder="Cth: Beli Sayuran Pasar">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" name="amount" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Deskripsi</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Nota/Bukti (Opsional)</label>
                        <input type="file" class="form-control" name="receipt_image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ url('/admin/expenses/categories') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" required placeholder="Cth: Transportasi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna Label</label>
                        <input type="color" class="form-control form-control-color" name="color" value="#0d6efd" title="Pilih warna">
                    </div>
                    
                    <hr>
                    <h6>Kategori Tersedia:</h6>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($categories as $cat)
                            <span class="badge" style="background-color: {{ $cat->color }}">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
