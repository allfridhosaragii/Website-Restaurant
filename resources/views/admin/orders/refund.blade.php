@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Refund Bill: #{{ $order->order_number }}</h1>
        <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Order
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Pilih Item untuk Direfund</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">Pilih Semua (Full Refund)</button>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/orders/'.$order->id.'/refund') }}" method="POST">
                        @csrf
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">Pilih</th>
                                        <th>Item</th>
                                        <th>Harga</th>
                                        <th>Qty Awal</th>
                                        <th width="20%">Qty Direfund</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input item-checkbox" type="checkbox" name="refund_items[{{ $item->id }}][selected]" value="1" id="item_{{ $item->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <label class="form-check-label" for="item_{{ $item->id }}">
                                                {{ $item->menu_name }}
                                            </label>
                                        </td>
                                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <input type="number" name="refund_items[{{ $item->id }}][quantity]" class="form-control item-qty" min="1" max="{{ $item->quantity }}" value="{{ $item->quantity }}" disabled>
                                            <input type="hidden" name="refund_items[{{ $item->id }}][price]" value="{{ $item->price }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <h6 class="font-weight-bold text-primary mb-3">Detail Refund</h6>
                        <div class="mb-3">
                            <label class="form-label">Alasan Refund <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan alasan pengembalian dana..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Isi Email dan Password Manager di bawah jika ingin Refund langsung disetujui secara otomatis (Otorisasi Langsung di Kasir). Jika dikosongkan, status Refund akan menjadi Pending dan harus disetujui via halaman Refund Requests.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Manager (Opsional)</label>
                                <input type="email" name="manager_email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Manager (Opsional)</label>
                                <input type="password" name="manager_password" class="form-control">
                            </div>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-warning" id="btnRefund" disabled>
                                <i class="fas fa-undo"></i> Ajukan Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Original Order</h6>
                </div>
                <div class="card-body">
                    <p><strong>Pelanggan:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Tipe Pesanan:</strong> {{ $order->type === 'dine_in' ? 'Dine In' : 'Take Away' }}</p>
                    <p><strong>Status Pembayaran:</strong> <span class="badge bg-success">Lunas</span></p>
                    <hr>
                    <p><strong>Total Saat Ini:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Nilai refund akan dihitung otomatis mencakup 10% Pajak yang sebelumnya dibayarkan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const btnRefund = document.getElementById('btnRefund');
        const btnSelectAll = document.getElementById('btnSelectAll');

        btnSelectAll.addEventListener('click', function() {
            let allChecked = true;
            checkboxes.forEach(cb => {
                if (!cb.checked) allChecked = false;
            });
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                const qtyInput = cb.closest('tr').querySelector('.item-qty');
                qtyInput.disabled = !cb.checked;
            });
            checkSelection();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const qtyInput = this.closest('tr').querySelector('.item-qty');
                qtyInput.disabled = !this.checked;
                checkSelection();
            });
        });

        function checkSelection() {
            let anyChecked = false;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    anyChecked = true;
                }
            });

            btnRefund.disabled = !anyChecked;
        }

        const qtyInputs = document.querySelectorAll('.item-qty');
        qtyInputs.forEach(input => {
            input.addEventListener('change', checkSelection);
            input.addEventListener('keyup', checkSelection);
        });
    });
</script>
@endpush
@endsection
