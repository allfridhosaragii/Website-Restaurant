@extends('layouts.admin')

@section('title', 'Manajemen Deposit Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan</h6>
            <form action="{{ url('/admin/deposits') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Saldo Deposit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td class="font-weight-bold text-success">Rp {{ number_format($customer->deposit_balance, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ url('/admin/deposits/' . $customer->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Kelola Deposit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data pelanggan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
