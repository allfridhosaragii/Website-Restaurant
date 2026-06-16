@extends('layouts.admin')
@section('title', $title ?? 'Laporan')
@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <a href="/admin/reports" class="text-decoration-none mb-3 d-inline-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard Laporan
            </a>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-primary">{{ $title ?? 'Laporan' }}</h4>
                <div class="d-flex gap-2">
                    <a href="?start_date={{ $start }}&end_date={{ $end }}&export=excel" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </a>
                    <a href="?start_date={{ $start }}&end_date={{ $end }}&export=pdf" class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
            
            <!-- Filter Tanggal -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form action="" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $end }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-filter"></i> Filter Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            @yield('report_summary')

            <!-- Chart -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Grafik {{ $title ?? 'Laporan' }}</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="reportChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            <!-- Tabel Detail -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">Tabel Detail</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        @yield('report_table')
                    </div>
                </div>
                @hasSection('report_pagination')
                <div class="card-footer bg-white border-0 py-3">
                    @yield('report_pagination')
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@yield('report_script')
@endsection
