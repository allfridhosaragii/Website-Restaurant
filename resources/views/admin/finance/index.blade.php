@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Keuangan</h1>
        
        <form method="GET" class="d-flex gap-2">
            <select name="month" class="form-select w-auto">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select w-auto">
                @for($y=date('Y'); $y>=date('Y')-2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Tampilkan</button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
        </form>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-4" id="financeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="pl-tab" data-bs-toggle="tab" data-bs-target="#pl" type="button" role="tab">Laba Rugi (Profit & Loss)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="balance-tab" data-bs-toggle="tab" data-bs-target="#balance" type="button" role="tab">Neraca (Balance Sheet)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="cashflow-tab" data-bs-toggle="tab" data-bs-target="#cashflow" type="button" role="tab">Arus Kas (Cash Flow)</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content" id="financeTabsContent">
        
        <!-- Profit & Loss Tab -->
        <div class="tab-pane fade show active" id="pl" role="tabpanel" tabindex="0">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan Laba Rugi - Periode {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm fs-5">
                            <tbody>
                                <tr class="border-bottom">
                                    <td colspan="2" class="fw-bold text-primary">PENDAPATAN</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Penjualan Order</td>
                                    <td class="text-end">Rp {{ number_format($orderIncome, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Deposit Top-up</td>
                                    <td class="text-end">Rp {{ number_format($depositTopupIncome, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top border-2 border-dark">
                                    <td class="ps-4 fw-bold">TOTAL PENDAPATAN</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2">&nbsp;</td></tr>

                                <tr class="border-bottom">
                                    <td colspan="2" class="fw-bold text-danger">BEBAN & PENGELUARAN</td>
                                </tr>
                                @forelse($expensesByCategory as $catName => $amount)
                                <tr>
                                    <td class="ps-4">{{ $catName }}</td>
                                    <td class="text-end">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="ps-4 text-muted fst-italic">Belum ada beban tercatat.</td>
                                    <td class="text-end">Rp 0</td>
                                </tr>
                                @endforelse
                                <tr class="border-top border-2 border-dark">
                                    <td class="ps-4 fw-bold">TOTAL BEBAN</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2">&nbsp;</td></tr>

                                <tr class="border-top border-3 border-primary bg-light">
                                    <td class="py-3 px-4 fw-bold fs-4">LABA BERSIH</td>
                                    <td class="text-end py-3 px-2 fw-bold fs-4 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Sheet Tab -->
        <div class="tab-pane fade" id="balance" role="tabpanel" tabindex="0">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Neraca - Per {{ date('t F Y', mktime(0,0,0,$month,1,$year)) }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2">AKTIVA (ASET)</h5>
                            <table class="table table-borderless table-sm fs-5">
                                <tr>
                                    <td>Kas Tunai (Berjalan)</td>
                                    <td class="text-end">Rp {{ number_format($cashBalance, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Persediaan Barang (Estimasi)</td>
                                    <td class="text-end">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top border-2 border-dark">
                                    <td class="fw-bold">TOTAL AKTIVA</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2">KEWAJIBAN & EKUITAS</h5>
                            <table class="table table-borderless table-sm fs-5">
                                <tr>
                                    <td colspan="2" class="fw-bold text-muted">Kewajiban</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Hutang Supplier</td>
                                    <td class="text-end">Rp {{ number_format($totalSupplierDebt, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Deposit Pelanggan (Titipan)</td>
                                    <td class="text-end">Rp {{ number_format($totalCustomerDeposit, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-muted">Total Kewajiban</td>
                                    <td class="text-end fw-bold text-muted">Rp {{ number_format($totalLiability, 0, ',', '.') }}</td>
                                </tr>
                                
                                <tr><td colspan="2">&nbsp;</td></tr>
                                
                                <tr>
                                    <td colspan="2" class="fw-bold text-muted">Ekuitas</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Modal Bersih / Laba Ditahan</td>
                                    <td class="text-end">Rp {{ number_format($equity, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold text-muted">Total Ekuitas</td>
                                    <td class="text-end fw-bold text-muted">Rp {{ number_format($equity, 0, ',', '.') }}</td>
                                </tr>
                                
                                <tr class="border-top border-2 border-dark mt-2">
                                    <td class="fw-bold">TOTAL KEWAJIBAN & EKUITAS</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($totalLiability + $equity, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Flow Tab -->
        <div class="tab-pane fade" id="cashflow" role="tabpanel" tabindex="0">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Arus Kas - Periode {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm fs-5">
                            <tbody>
                                <tr class="bg-light border-bottom">
                                    <td class="py-2 px-3 fw-bold">SALDO AWAL ({{ date('01 F Y', mktime(0,0,0,$month,1,$year)) }})</td>
                                    <td class="py-2 px-3 text-end fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2">&nbsp;</td></tr>

                                <tr class="border-bottom">
                                    <td colspan="2" class="fw-bold text-success">KAS MASUK</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Dari Penjualan Tunai / Gateway</td>
                                    <td class="text-end">Rp {{ number_format($orderIncome, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Dari Deposit Top-up Pelanggan</td>
                                    <td class="text-end">Rp {{ number_format($depositTopupIncome, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top border-2 border-dark">
                                    <td class="ps-4 fw-bold">TOTAL KAS MASUK</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2">&nbsp;</td></tr>

                                <tr class="border-bottom">
                                    <td colspan="2" class="fw-bold text-danger">KAS KELUAR</td>
                                </tr>
                                <tr>
                                    <td class="ps-4">Pengeluaran Operasional & Hutang</td>
                                    <td class="text-end">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="border-top border-2 border-dark">
                                    <td class="ps-4 fw-bold">TOTAL KAS KELUAR</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                                </tr>

                                <tr><td colspan="2">&nbsp;</td></tr>
                                
                                <tr class="border-top">
                                    <td class="py-2 fw-bold text-muted">PERUBAHAN KAS</td>
                                    <td class="text-end py-2 fw-bold text-muted">
                                        {{ $netCashFlow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr class="border-top border-3 border-primary bg-light">
                                    <td class="py-3 px-4 fw-bold fs-4">SALDO AKHIR ({{ date('t F Y', mktime(0,0,0,$month,1,$year)) }})</td>
                                    <td class="text-end py-3 px-3 fw-bold fs-4 text-primary">
                                        Rp {{ number_format($endingBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .navbar, .sidebar, .btn, form, .nav-tabs {
            display: none !important;
        }
        .tab-content > .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endpush
