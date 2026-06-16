@extends('layouts.admin')
@section('title', 'Edit Diskon')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <a href="/admin/discounts" class="text-decoration-none mb-3 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Diskon
                </a>
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 text-primary">Edit Diskon</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="/admin/discounts/{{ $discount->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Diskon / Promo <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $discount->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kode Voucher (Opsional)</label>
                                    <input type="text" name="voucher_code" class="form-control text-uppercase @error('voucher_code') is-invalid @enderror" value="{{ old('voucher_code', $discount->voucher_code) }}" placeholder="Misal: PROMO2024">
                                    <small class="text-muted">Kosongkan jika ini diskon otomatis tanpa kode.</small>
                                    @error('voucher_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Tipe Diskon <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="fixed" {{ old('type', $discount->type) == 'fixed' ? 'selected' : '' }}>Fixed (Nominal Rp)</option>
                                        <option value="percentage" {{ old('type', $discount->type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="free_item" {{ old('type', $discount->type) == 'free_item' ? 'selected' : '' }}>Gratis Item (Voucher Produk)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nilai Diskon <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value', $discount->value) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cakupan (Scope) <span class="text-danger">*</span></label>
                                    <select name="scope" id="scopeSelect" class="form-select @error('scope') is-invalid @enderror" required>
                                        <option value="order" {{ old('scope', $discount->scope) == 'order' ? 'selected' : '' }}>Seluruh Pesanan (Order Level)</option>
                                        <option value="item" {{ old('scope', $discount->scope) == 'item' ? 'selected' : '' }}>Menu Tertentu (Item Level)</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="menuSelectContainer" style="display: {{ old('scope', $discount->scope) == 'item' ? 'block' : 'none' }};">
                                    <label class="form-label">Pilih Menu <span class="text-danger">*</span></label>
                                    <select name="menu_id" class="form-select @error('menu_id') is-invalid @enderror">
                                        <option value="">-- Pilih Menu --</option>
                                        @foreach($menus as $menu)
                                            <option value="{{ $menu->id }}" {{ old('menu_id', $discount->menu_id) == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('menu_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3" id="freeMenuSelectContainer" style="display: {{ old('type', $discount->type) == 'free_item' ? 'block' : 'none' }};">
                                    <label class="form-label">Menu Gratis <span class="text-danger">*</span></label>
                                    <select name="free_menu_id" class="form-select @error('free_menu_id') is-invalid @enderror">
                                        <option value="">-- Pilih Menu Gratis --</option>
                                        @foreach($menus as $menu)
                                            <option value="{{ $menu->id }}" {{ old('free_menu_id', $discount->free_menu_id) == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('free_menu_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Pengaturan Waktu & Kuota</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Mulai (Opsional)</label>
                                    <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $discount->start_date) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Berakhir (Opsional)</label>
                                    <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $discount->end_date) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Happy Hour Mulai (Opsional)</label>
                                    <input type="time" name="happy_hour_start" class="form-control @error('happy_hour_start') is-invalid @enderror" value="{{ old('happy_hour_start', $discount->happy_hour_start ? substr($discount->happy_hour_start, 0, 5) : '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Happy Hour Selesai (Opsional)</label>
                                    <input type="time" name="happy_hour_end" class="form-control @error('happy_hour_end') is-invalid @enderror" value="{{ old('happy_hour_end', $discount->happy_hour_end ? substr($discount->happy_hour_end, 0, 5) : '') }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Hari Berlaku (Opsional)</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 0=>'Minggu']; 
                                        $oldDays = old('days_of_week', $discount->days_of_week ?? []);
                                        @endphp
                                        @foreach($days as $val => $label)
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" name="days_of_week[]" value="{{ $val }}" id="day{{ $val }}" {{ in_array($val, $oldDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day{{ $val }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted d-block mt-1">Kosongkan jika berlaku setiap hari.</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Limit Total</label>
                                    <input type="number" name="usage_limit" min="1" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit', $discount->usage_limit) }}" placeholder="Maks Kuota">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Limit per User</label>
                                    <input type="number" name="max_usage_per_user" min="0" class="form-control @error('max_usage_per_user') is-invalid @enderror" value="{{ old('max_usage_per_user', $discount->max_usage_per_user) }}" placeholder="0 = No Limit">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status Aktif</label>
                                    <select name="is_active" class="form-select">
                                        <option value="1" {{ old('is_active', $discount->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('is_active', $discount->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-2"></i> Perbarui Diskon
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scopeSelect = document.getElementById('scopeSelect');
        const menuSelectContainer = document.getElementById('menuSelectContainer');
        const menuSelect = document.querySelector('select[name="menu_id"]');

        scopeSelect.addEventListener('change', function() {
            if (this.value === 'item') {
                menuSelectContainer.style.display = 'block';
                menuSelect.setAttribute('required', 'required');
            } else {
                menuSelectContainer.style.display = 'none';
                menuSelect.removeAttribute('required');
                menuSelect.value = '';
            }
        });
        
        const typeSelect = document.querySelector('select[name="type"]');
        const freeMenuContainer = document.getElementById('freeMenuSelectContainer');
        const freeMenuSelect = document.querySelector('select[name="free_menu_id"]');
        const valueInput = document.querySelector('input[name="value"]');
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'free_item') {
                freeMenuContainer.style.display = 'block';
                freeMenuSelect.setAttribute('required', 'required');
                valueInput.value = '0';
                valueInput.setAttribute('readonly', 'readonly');
                scopeSelect.value = 'order'; // forced to order
                scopeSelect.setAttribute('disabled', 'disabled');
                menuSelectContainer.style.display = 'none';
            } else {
                freeMenuContainer.style.display = 'none';
                freeMenuSelect.removeAttribute('required');
                if (valueInput.value == '0') valueInput.value = ''; // clear only if 0
                valueInput.removeAttribute('readonly');
                scopeSelect.removeAttribute('disabled');
            }
        });
        
        // Ensure disabled select is still sent if we need its value
        document.querySelector('form').addEventListener('submit', function() {
            scopeSelect.removeAttribute('disabled');
        });
        
        // Trigger initial state
        typeSelect.dispatchEvent(new Event('change'));
    });
</script>
@endsection
