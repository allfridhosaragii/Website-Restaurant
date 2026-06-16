@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Manajemen Review & Rating</h2>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th>Pesanan / Menu</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>
                                <strong>{{ $review->user ? $review->user->name : 'Unknown' }}</strong>
                                <br><small class="text-muted">{{ $review->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                @if($review->order)
                                    <span class="badge bg-secondary">Order #{{ $review->order->order_number }}</span>
                                @endif
                                @if($review->menu)
                                    <br><small class="text-muted">{{ $review->menu->name }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <p class="mb-1" style="max-width: 300px; white-space: normal;">
                                    {{ $review->comment ?? '-' }}
                                </p>
                                @if($review->admin_reply)
                                    <div class="bg-light p-2 rounded small mt-1">
                                        <strong><i class="bi bi-reply-fill text-primary"></i> Balasan:</strong>
                                        {{ $review->admin_reply }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($review->is_approved)
                                    <span class="badge bg-success">Ditampilkan</span>
                                @else
                                    <span class="badge bg-danger">Disembunyikan</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Balas">
                                        <i class="bi bi-reply"></i>
                                    </button>
                                    <form action="{{ url('/admin/reviews/'.$review->id.'/toggle-status') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $review->is_approved ? 'danger' : 'success' }}" title="{{ $review->is_approved ? 'Sembunyikan' : 'Tampilkan' }}">
                                            <i class="bi bi-{{ $review->is_approved ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Reply Modal -->
                                <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Balas Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('/admin/reviews/'.$review->id.'/reply') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Review dari {{ $review->user ? $review->user->name : 'Unknown' }}</label>
                                                        <div class="p-3 bg-light rounded">
                                                            <div class="text-warning mb-1">
                                                                @for($i=1; $i<=5; $i++)
                                                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                                @endfor
                                                            </div>
                                                            {{ $review->comment }}
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Balasan Anda</label>
                                                        <textarea name="admin_reply" class="form-control" rows="4" required>{{ $review->admin_reply }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Balasan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada review
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
