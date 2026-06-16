@extends('layouts.admin')
@section('title', 'Manajemen Diskon')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="bi bi-tag-fill me-2 text-primary"></i> Manajemen Diskon</h3>
                <a href="/admin/discounts/create" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Diskon
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Nama Diskon</th>
                                <th class="py-3">Tipe & Nilai</th>
                                <th class="py-3">Scope / Voucher</th>
                                <th class="py-3">Masa Berlaku</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discounts as $discount)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold">{{ $discount->name }}</div>
                                        @if($discount->usage_limit)
                                            <small class="text-muted">Terpakai: {{ $discount->usage_count }}/{{ $discount->usage_limit }}</small>
                                        @else
                                            <small class="text-muted">Terpakai: {{ $discount->usage_count }} (Tanpa Batas)</small>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($discount->type === 'fixed')
                                            <span class="badge bg-secondary">Fixed</span> Rp {{ number_format($discount->value, 0, ',', '.') }}
                                        @else
                                            <span class="badge bg-info">Persentase</span> {{ rtrim(rtrim(number_format($discount->value, 2, ',', '.'), '0'), ',') }}%
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div>
                                            @if($discount->scope === 'order')
                                                <span class="badge bg-primary">Order</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Item: {{ $discount->menu_name ?? 'Semua' }}</span>
                                            @endif
                                        </div>
                                        @if($discount->voucher_code)
                                            <div class="mt-1"><small>Kode: <strong>{{ $discount->voucher_code }}</strong></small></div>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($discount->start_date || $discount->end_date)
                                            <small class="d-block">
                                                {{ $discount->start_date ? \Carbon\Carbon::parse($discount->start_date)->format('d/m/y H:i') : 'Kapan saja' }} - 
                                                {{ $discount->end_date ? \Carbon\Carbon::parse($discount->end_date)->format('d/m/y H:i') : 'Selamanya' }}
                                            </small>
                                        @endif
                                        @if($discount->happy_hour_start)
                                            <small class="d-block text-primary">
                                                <i class="bi bi-clock"></i> {{ substr($discount->happy_hour_start, 0, 5) }} - {{ substr($discount->happy_hour_end, 0, 5) }}
                                            </small>
                                        @endif
                                        @if(!$discount->start_date && !$discount->end_date && !$discount->happy_hour_start)
                                            <small class="text-muted">Tidak terbatas waktu</small>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($discount->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-end px-4">
                                        <a href="/admin/discounts/{{ $discount->id }}/edit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/admin/discounts/{{ $discount->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus diskon ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-tag fs-1 d-block mb-3"></i>
                                        Belum ada diskon yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
