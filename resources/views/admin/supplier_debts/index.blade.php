@extends('layouts.admin')

@section('title', 'Hutang Supplier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Hutang Supplier</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDebtModal">
            <i class="bi bi-plus-lg"></i> Catat Hutang Baru
        </button>
    </div>

    <!-- Summary Widgets -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Hutang Berjalan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalDebt, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-minus fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Jatuh Tempo (Overdue)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($overdueDebt, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Hutang</h6>
            <form method="GET" class="d-flex align-items-center gap-2">
                <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar (Unpaid)</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sebagian (Partial)</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Jatuh Tempo (Overdue)</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Supplier & Item</th>
                            <th>Jatuh Tempo</th>
                            <th>Total Hutang</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Hutang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $debt->supplier_name }}</strong><br>
                                <small>{{ $debt->title }}</small>
                            </td>
                            <td>
                                {{ $debt->due_date->format('d M Y') }}
                                @if($debt->due_date < now() && $debt->status !== 'paid')
                                <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
                            <td class="text-success">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</td>
                            <td class="text-danger fw-bold">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($debt->status === 'paid')
                                <span class="badge bg-success">Lunas</span>
                                @elseif($debt->status === 'partial')
                                <span class="badge bg-warning text-dark">Parsial</span>
                                @elseif($debt->status === 'overdue')
                                <span class="badge bg-danger">Jatuh Tempo</span>
                                @else
                                <span class="badge bg-secondary">Belum Bayar</span>
                                @endif
                            </td>
                            <td>
                                @if($debt->status !== 'paid')
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal{{ $debt->id }}" title="Bayar">
                                    <i class="bi bi-wallet2"></i> Bayar
                                </button>
                                @endif
                                
                                <form action="{{ url('/admin/supplier-debts/'.$debt->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan hutang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Pay Modal -->
                        @if($debt->status !== 'paid')
                        <div class="modal fade" id="payModal{{ $debt->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ url('/admin/supplier-debts/'.$debt->id.'/pay') }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Pembayaran Hutang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-1"><strong>Supplier:</strong> {{ $debt->supplier_name }}</p>
                                            <p class="mb-3"><strong>Item:</strong> {{ $debt->title }}</p>
                                            <div class="alert alert-warning py-2 mb-3">
                                                Sisa Hutang: <strong>Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</strong>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Pembayaran (Rp)</label>
                                                <input type="number" class="form-control" name="payment_amount" required min="1" max="{{ $debt->remaining_amount }}" value="{{ $debt->remaining_amount }}">
                                                <small class="text-muted">Catatan: Pembayaran akan otomatis dicatat sebagai Pengeluaran Operasional (Kategori: Pembayaran Hutang).</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Konfirmasi Pembayaran</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data hutang supplier.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Debt Modal -->
<div class="modal fade" id="addDebtModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ url('/admin/supplier-debts') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catat Hutang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" class="form-control" name="supplier_name" required placeholder="Cth: PT Makmur Daging">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Pembelian / Transaksi</label>
                        <input type="text" class="form-control" name="title" required placeholder="Cth: Pembelian Daging 50kg">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Hutang (Rp)</label>
                            <input type="number" class="form-control" name="amount" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Nota Pembelian (Opsional)</label>
                        <input type="file" class="form-control" name="receipt_image" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Hutang</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
