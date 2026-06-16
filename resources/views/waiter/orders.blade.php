@extends('layouts.admin')

@section('title', 'Daftar Pesanan Saya')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pesanan (Waiter)</h1>
        <a href="{{ route('waiter.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Pesanan Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tipe / Meja</th>
                            <th>Status Dapur</th>
                            <th>Status Pembayaran</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>
                                @if($order->type == 'dine_in')
                                    Dine In (Meja {{ $order->table_number ?? $order->table->number ?? '?' }})
                                @else
                                    Take Away
                                @endif
                            </td>
                            <td>
                                @if($order->status == 'pending' || $order->status == 'processing')
                                    <span class="badge badge-warning">Diproses Dapur</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge badge-success">Lunas</span>
                                @else
                                    <span class="badge badge-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada riwayat pesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
