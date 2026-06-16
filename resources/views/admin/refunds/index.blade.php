@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pengajuan Refund</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Refund Requests</h6>
            <div class="dropdown no-arrow">
                <form action="{{ url('/admin/refunds') }}" method="GET" class="d-inline-block">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Disetujui / Selesai</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No Order</th>
                            <th>Tipe</th>
                            <th>Total Refund</th>
                            <th>Alasan</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                        <tr>
                            <td>
                                <a href="{{ url('/admin/orders/'.$refund->order_id) }}">#{{ $refund->order_number }}</a>
                                <br><small class="text-muted">{{ date('d M Y H:i', strtotime($refund->created_at)) }}</small>
                            </td>
                            <td>
                                @if($refund->type === 'full')
                                <span class="badge bg-primary">Full Refund</span>
                                @else
                                <span class="badge bg-info">Partial Refund</span>
                                @endif
                            </td>
                            <td><strong>Rp {{ number_format($refund->amount, 0, ',', '.') }}</strong></td>
                            <td>{{ Str::limit($refund->reason, 50) }}</td>
                            <td>{{ $refund->kasir_name }}</td>
                            <td>
                                @if($refund->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($refund->status === 'completed')
                                <span class="badge bg-success">Disetujui</span>
                                <br><small class="text-muted">oleh {{ $refund->manager_name }}</small>
                                @elseif($refund->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                                <br><small class="text-muted">oleh {{ $refund->manager_name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($refund->status === 'pending')
                                <div class="btn-group" role="group">
                                    <form action="{{ url('/admin/refunds/'.$refund->id.'/approve') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui refund ini dan kembalikan stok?')">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $refund->id }}">
                                        <i class="bi bi-x"></i> Reject
                                    </button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Tolak Pengajuan Refund</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('/admin/refunds/'.$refund->id.'/reject') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak Refund</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data refund.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
