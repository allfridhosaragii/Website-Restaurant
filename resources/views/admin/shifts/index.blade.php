@extends('layouts.admin')

@section('title', 'Riwayat Shift')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Shift Kasir</h1>
    </div>

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.shifts.index') }}" class="row align-items-end g-2">
                <div class="col-md-4">
                    <label class="form-label mb-1">Filter Kasir</label>
                    <select name="user_id" class="form-control">
                        <option value="">Semua Kasir</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ request('user_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->role ?? 'admin' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-block">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Shift</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Kasir</th>
                            <th>Mulai Shift</th>
                            <th>Tutup Shift</th>
                            <th>Durasi</th>
                            <th class="text-right">Modal Awal</th>
                            <th class="text-right">Exp. Cash</th>
                            <th class="text-right">Kas Aktual</th>
                            <th class="text-right">Selisih</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $shift->user->name }}</div>
                                <small class="text-muted">{{ $shift->user->role ?? 'admin' }}</small>
                            </td>
                            <td>{{ $shift->clock_in->format('d/m/Y H:i') }}</td>
                            <td>{{ $shift->clock_out ? $shift->clock_out->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $shift->duration }}</td>
                            <td class="text-right">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                            <td class="text-right">
                                {{ $shift->expected_cash !== null ? 'Rp ' . number_format($shift->expected_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right">
                                {{ $shift->closing_cash !== null ? 'Rp ' . number_format($shift->closing_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right font-weight-bold">
                                @if($shift->cash_difference !== null)
                                    @php $diff = $shift->cash_difference; @endphp
                                    <span class="{{ $diff == 0 ? 'text-success' : ($diff > 0 ? 'text-primary' : 'text-danger') }}">
                                        {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->status === 'active')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Selesai</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.shifts.show', $shift->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada data shift.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $shifts->links() }}
        </div>
    </div>
</div>
@endsection
