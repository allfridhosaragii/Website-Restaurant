@extends('layouts.admin')

@section('title', 'Detail Shift')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Shift — {{ $shift->user->name }}</h1>
        <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Ringkasan Shift</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Kasir</td><td class="font-weight-bold">{{ $shift->user->name }}</td></tr>
                        <tr><td>Mulai</td><td>{{ $shift->clock_in->format('d/m/Y H:i') }}</td></tr>
                        <tr><td>Tutup</td><td>{{ $shift->clock_out ? $shift->clock_out->format('d/m/Y H:i') : 'Masih Aktif' }}</td></tr>
                        <tr><td>Durasi</td><td>{{ $shift->duration }}</td></tr>
                        <tr><td>Status</td><td>
                            <span class="badge badge-{{ $shift->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $shift->status === 'active' ? 'Aktif' : 'Selesai' }}
                            </span>
                        </td></tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Rekap Kas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Modal Awal</td><td class="text-right font-weight-bold">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</td></tr>
                        <tr><td>Kas Seharusnya</td><td class="text-right">{{ $shift->expected_cash !== null ? 'Rp ' . number_format($shift->expected_cash, 0, ',', '.') : '-' }}</td></tr>
                        <tr><td>Kas Aktual</td><td class="text-right">{{ $shift->closing_cash !== null ? 'Rp ' . number_format($shift->closing_cash, 0, ',', '.') : '-' }}</td></tr>
                        <tr class="border-top">
                            <td class="font-weight-bold">Selisih</td>
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
                        </tr>
                    </table>
                    @if($shift->notes)
                    <div class="alert alert-light mt-3 mb-0">
                        <small><strong>Catatan:</strong> {{ $shift->notes }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Order dalam Shift ini ({{ $orders->count() }} order)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No. Order</th>
                                    <th>Waktu</th>
                                    <th>Tipe</th>
                                    <th>Metode Bayar</th>
                                    <th class="text-right">Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
                                    <td>{{ $order->type == 'dine_in' ? 'Dine In' : 'Take Away' }}</td>
                                    <td>{{ strtoupper($order->payment_method ?? '-') }}</td>
                                    <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td><span class="badge badge-{{ $order->status == 'completed' ? 'success' : 'warning' }}">{{ $order->status }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada order dalam shift ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
