@extends('layouts.admin')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Absensi Karyawan</h1>
        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-camera"></i> Absensi Saya
        </a>
    </div>

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="row align-items-end g-2">
                <div class="col-md-3">
                    <label class="form-label mb-1">Karyawan</label>
                    <select name="user_id" class="form-control">
                        <option value="">Semua Karyawan</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ request('user_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Bulan</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absen</option>
                        <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Cuti</option>
                        <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-block">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Absensi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status</th>
                            <th>Foto Check-In</th>
                            <th>Foto Check-Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                        <tr>
                            <td>{{ $rec->user->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($rec->date)->format('d/m/Y') }}</td>
                            <td>{{ $rec->check_in }}</td>
                            <td>{{ $rec->check_out ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $rec->status_badge }}">{{ ucfirst($rec->status) }}</span>
                            </td>
                            <td class="text-center">
                                @if($rec->check_in_photo)
                                    <a href="{{ Storage::url($rec->check_in_photo) }}" target="_blank">
                                        <img src="{{ Storage::url($rec->check_in_photo) }}" style="height:40px; width:40px; object-fit:cover; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($rec->check_out_photo)
                                    <a href="{{ Storage::url($rec->check_out_photo) }}" target="_blank">
                                        <img src="{{ Storage::url($rec->check_out_photo) }}" style="height:40px; width:40px; object-fit:cover; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data absensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
