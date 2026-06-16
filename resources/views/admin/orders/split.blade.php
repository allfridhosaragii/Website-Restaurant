@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Split Bill: #{{ $order->order_number }}</h1>
        <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Order
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pilih Item untuk Dipisah</h6>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/orders/'.$order->id.'/split') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">Pilih</th>
                                        <th>Item</th>
                                        <th>Harga</th>
                                        <th>Qty Awal</th>
                                        <th width="20%">Qty Dipindah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input item-checkbox" type="checkbox" name="split_items[{{ $item->id }}][selected]" value="1" id="item_{{ $item->id }}">
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
                                            <input type="number" name="split_items[{{ $item->id }}][quantity]" class="form-control item-qty" min="1" max="{{ $item->quantity }}" value="1" disabled>
                                            <input type="hidden" name="split_items[{{ $item->id }}][price]" value="{{ $item->price }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary" id="btnSplit" disabled>
                                <i class="fas fa-cut"></i> Proses Split Bill
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
                    <p><strong>Meja:</strong> {{ $order->table_number ?: '-' }}</p>
                    <hr>
                    <p><strong>Total Saat Ini:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    <div class="alert alert-info">
                        <strong>Catatan:</strong> Item yang dipilih akan dipindahkan ke bill baru. Harga dan pajak pada bill lama dan baru akan dihitung ulang secara otomatis.
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
        const btnSplit = document.getElementById('btnSplit');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const qtyInput = this.closest('tr').querySelector('.item-qty');
                qtyInput.disabled = !this.checked;
                
                checkSelection();
            });
        });

        function checkSelection() {
            let anyChecked = false;
            let allCheckedWithMaxQty = true;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    anyChecked = true;
                    const qtyInput = checkbox.closest('tr').querySelector('.item-qty');
                    if (parseInt(qtyInput.value) < parseInt(qtyInput.getAttribute('max'))) {
                        allCheckedWithMaxQty = false;
                    }
                } else {
                    allCheckedWithMaxQty = false;
                }
            });

            // If user checks ALL items with their maximum quantities, we prevent it because an order cannot be completely split into a new one leaving original order empty.
            if (anyChecked && !allCheckedWithMaxQty) {
                btnSplit.disabled = false;
            } else if (anyChecked && allCheckedWithMaxQty) {
                // Warning, cannot split all items fully
                btnSplit.disabled = true;
                alert("Tidak dapat memecah semua item sekaligus. Sisakan minimal 1 item di order asli.");
            } else {
                btnSplit.disabled = true;
            }
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
