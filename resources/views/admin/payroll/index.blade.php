@extends('layouts.admin')

@section('title', 'Manajemen Payroll')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payroll / Penggajian</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Controls --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3">
            <div class="row align-items-end g-2">
                <div class="col-md-4">
                    <form method="GET" action="{{ route('admin.payroll.index') }}" class="d-flex gap-2">
                        <input type="month" name="month" class="form-control" value="{{ $month }}">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route('admin.payroll.generate') }}">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Generate payroll untuk bulan {{ $month }}?')">
                            <i class="fas fa-magic"></i> Generate Payroll Bulan Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Payroll — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Karyawan</th>
                            <th class="text-right">Gaji Pokok</th>
                            <th class="text-right">Bonus Hadir</th>
                            <th class="text-right">Tunjangan</th>
                            <th class="text-right">Potongan</th>
                            <th class="text-right font-weight-bold">Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $p)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $p->employee_name }}</div>
                                <small class="text-muted">{{ $p->employee_role ?? 'staff' }}</small>
                            </td>
                            <td class="text-right">Rp {{ number_format($p->base_salary, 0, ',', '.') }}</td>
                            <td class="text-right text-success">+Rp {{ number_format($p->attendance_bonus, 0, ',', '.') }}</td>
                            <td class="text-right text-info">+Rp {{ number_format($p->allowance, 0, ',', '.') }}</td>
                            <td class="text-right text-danger">-Rp {{ number_format($p->deduction, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($p->total_salary, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badge = ['draft'=>'secondary','approved'=>'info','paid'=>'success'][$p->status] ?? 'light';
                                    $label = ['draft'=>'Draft','approved'=>'Disetujui','paid'=>'Dibayar'][$p->status] ?? $p->status;
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ $label }}</span>
                                @if($p->paid_at)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($p->paid_at)->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payroll.slip', $p->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Cetak Slip">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($p->status === 'draft')
                                    <form method="POST" action="{{ route('admin.payroll.approve', $p->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-info" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($p->status === 'approved')
                                    <form method="POST" action="{{ route('admin.payroll.pay', $p->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Bayar" onclick="return confirm('Konfirmasi pembayaran gaji?')">
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data payroll. Klik "Generate Payroll" untuk membuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
