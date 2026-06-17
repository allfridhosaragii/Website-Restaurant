@extends('layouts.admin')

@section('title', 'Metode Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Metode Pembayaran</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-lg"></i> Tambah Metode
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80">Urutan</th>
                        <th>Nama Metode</th>
                        <th width="150" class="text-center">Status</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentMethods as $pm)
                    <tr>
                        <td class="text-center">{{ $pm->sort_order }}</td>
                        <td class="fw-bold">{{ $pm->name }}</td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input toggle-status" type="checkbox" role="switch" data-id="{{ $pm->id }}" {{ $pm->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $pm->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.payment_methods.destroy', $pm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus metode pembayaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    @push('modals')
                    <div class="modal fade" id="editModal{{ $pm->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.payment_methods.update', $pm->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Metode Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Metode</label>
                                            <input type="text" class="form-control" name="name" value="{{ $pm->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Urutan Tampil (Sort Order)</label>
                                            <input type="number" class="form-control" name="sort_order" value="{{ $pm->sort_order }}" required>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="active{{ $pm->id }}" {{ $pm->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active{{ $pm->id }}">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endpush
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada metode pembayaran yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
@push('modals')
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.payment_methods.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Metode Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Metode</label>
                        <input type="text" class="form-control" name="name" required placeholder="Contoh: Transfer BCA, Tunai, EDC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan Tampil (Sort Order)</label>
                        <input type="number" class="form-control" name="sort_order" value="0" required>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="activeNew" checked>
                        <label class="form-check-label" for="activeNew">Aktif</label>
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
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/admin/payment_methods/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    this.checked = !isActive;
                    alert('Gagal mengubah status');
                }
            })
            .catch(err => {
                this.checked = !isActive;
                alert('Terjadi kesalahan jaringan');
            });
        });
    });
</script>
@endpush
@endsection
