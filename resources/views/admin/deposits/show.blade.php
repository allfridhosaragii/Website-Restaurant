@extends('layouts.admin')

@section('title', 'Detail Deposit Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ url('/admin/deposits') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Informasi Pelanggan</div>
                    <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $customer->name }}</div>
                    <div class="mb-3">{{ $customer->email }}</div>
                    
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 mt-4">Saldo Deposit Saat Ini</div>
                    <div class="h3 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($customer->deposit_balance, 0, ',', '.') }}</div>
                    
                    <hr class="my-4">
                    
                    <form action="{{ url('/admin/deposits/'.$customer->id.'/topup') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Topup Saldo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="amount" class="form-control" required min="1000" step="1000" placeholder="100000">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="description" class="form-control" placeholder="Opsional...">
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-plus"></i> Tambah Deposit</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi Deposit</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Saldo Setelah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                <tr>
                                    <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($trx->type == 'topup')
                                            <span class="badge badge-success">Topup</span>
                                        @elseif($trx->type == 'payment')
                                            <span class="badge badge-danger">Pembayaran</span>
                                        @elseif($trx->type == 'refund')
                                            <span class="badge badge-info">Refund</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $trx->type }}</span>
                                        @endif
                                    </td>
                                    <td class="{{ $trx->type == 'payment' ? 'text-danger' : 'text-success' }}">
                                        {{ $trx->type == 'payment' ? '-' : '+' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $trx->description }}</td>
                                    <td>Rp {{ number_format($trx->balance_after, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada riwayat transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
