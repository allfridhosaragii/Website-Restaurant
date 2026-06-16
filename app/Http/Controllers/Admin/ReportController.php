<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        $totalPenjualanHariIni = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total');
            
        $totalTransaksi = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->count();
            
        $rataRataPerTransaksi = $totalTransaksi > 0 ? $totalPenjualanHariIni / $totalTransaksi : 0;
        
        $pelangganBaru = DB::table('users')
            ->where('role', 'customer')
            ->whereDate('created_at', $today)
            ->count();

        // Chart Penjualan Minggu Ini
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $salesThisWeek = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $labels = [];
        $data = [];
        foreach ($salesThisWeek as $sale) {
            $labels[] = Carbon::parse($sale->date)->format('D, d M');
            $data[] = $sale->total;
        }

        return view('admin.reports.index', compact(
            'totalPenjualanHariIni',
            'totalTransaksi',
            'rataRataPerTransaksi',
            'pelangganBaru',
            'labels',
            'data'
        ));
    }

    public function sales(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SalesExport($startDate, $endDate), 'laporan-penjualan.xlsx');
            } elseif ($request->export == 'pdf') {
                $sales = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'asc')
                    ->get();
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.sales', compact('sales', 'start', 'end'));
                return $pdf->download('laporan-penjualan.pdf');
            }
        }

        // Chart Data
        $salesChartData = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $labels = [];
        $data = [];
        $totalSales = 0;
        foreach ($salesChartData as $sale) {
            $labels[] = Carbon::parse($sale->date)->format('d M Y');
            $data[] = $sale->total;
            $totalSales += $sale->total;
        }

        // Table Data
        $orders = Order::with(['customer', 'cashier'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.sales', compact('start', 'end', 'labels', 'data', 'orders', 'totalSales'));
    }

    public function products(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $topProducts = \App\Models\OrderItem::whereHas('order', function($q) use($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('menu_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->with('menu') // Load menu relation to get names
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductExport($startDate, $endDate), 'laporan-produk-terlaris.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.products', compact('topProducts', 'start', 'end'));
                return $pdf->download('laporan-produk-terlaris.pdf');
            }
        }

        $labels = [];
        $data = [];
        foreach ($topProducts as $product) {
            $labels[] = $product->menu ? $product->menu->name : 'Unknown';
            $data[] = $product->total_qty;
        }

        return view('admin.reports.products', compact('start', 'end', 'labels', 'data', 'topProducts'));
    }

    public function peakHour(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $peakHours = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total_orders, SUM(total) as total_sales')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PeakHourExport($startDate, $endDate), 'laporan-jam-sibuk.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.peak_hour', compact('peakHours', 'start', 'end'));
                return $pdf->download('laporan-jam-sibuk.pdf');
            }
        }

        // Initialize 24 hours with 0
        $hourlyData = array_fill(0, 24, ['orders' => 0, 'sales' => 0]);
        foreach ($peakHours as $ph) {
            $hourlyData[(int)$ph->hour]['orders'] = $ph->total_orders;
            $hourlyData[(int)$ph->hour]['sales'] = $ph->total_sales;
        }

        $labels = [];
        $dataOrders = [];
        $dataSales = [];
        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $dataOrders[] = $hourlyData[$i]['orders'];
            $dataSales[] = $hourlyData[$i]['sales'];
        }

        return view('admin.reports.peak_hour', compact('start', 'end', 'labels', 'dataOrders', 'dataSales', 'peakHours'));
    }

    public function payments(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $paymentMethods = \App\Models\OrderPayment::whereHas('order', function($q) use($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PaymentExport($startDate, $endDate), 'laporan-pembayaran.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.payments', compact('paymentMethods', 'start', 'end'));
                return $pdf->download('laporan-pembayaran.pdf');
            }
        }

        $labels = [];
        $data = [];
        foreach ($paymentMethods as $pm) {
            // format method name (e.g. credit_card -> Credit Card)
            $methodName = ucwords(str_replace('_', ' ', $pm->payment_method));
            $labels[] = $methodName;
            $data[] = $pm->total;
        }

        return view('admin.reports.payments', compact('start', 'end', 'labels', 'data', 'paymentMethods'));
    }

    public function employees(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $employeeSales = Order::with('cashier')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('cashier_id')
            ->selectRaw('cashier_id, SUM(total) as total_sales, COUNT(*) as total_orders')
            ->groupBy('cashier_id')
            ->orderByDesc('total_sales')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\EmployeeExport($startDate, $endDate), 'laporan-karyawan.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.employees', compact('employeeSales', 'start', 'end'));
                return $pdf->download('laporan-karyawan.pdf');
            }
        }

        $labels = [];
        $dataSales = [];
        $dataOrders = [];
        foreach ($employeeSales as $es) {
            $labels[] = $es->cashier ? $es->cashier->name : 'Unknown';
            $dataSales[] = $es->total_sales;
            $dataOrders[] = $es->total_orders;
        }

        return view('admin.reports.employees', compact('start', 'end', 'labels', 'dataSales', 'dataOrders', 'employeeSales'));
    }

    public function tables(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $tableSales = Order::with('table')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('table_id')
            ->selectRaw('table_id, SUM(total) as total_sales, COUNT(*) as total_orders')
            ->groupBy('table_id')
            ->orderByDesc('total_sales')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TableExport($startDate, $endDate), 'laporan-meja.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.tables', compact('tableSales', 'start', 'end'));
                return $pdf->download('laporan-meja.pdf');
            }
        }

        $labels = [];
        $dataSales = [];
        $dataOrders = [];
        foreach ($tableSales as $ts) {
            $labels[] = $ts->table ? 'Meja ' . $ts->table->table_number : 'Unknown';
            $dataSales[] = $ts->total_sales;
            $dataOrders[] = $ts->total_orders;
        }

        return view('admin.reports.tables', compact('start', 'end', 'labels', 'dataSales', 'dataOrders', 'tableSales'));
    }

    public function discounts(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $discountUsages = \App\Models\DiscountUsage::with(['discount', 'order'])
            ->whereHas('order', function($q) use($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('discount_id, COUNT(*) as usage_count, SUM(discount_amount) as total_discount')
            ->groupBy('discount_id')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DiscountExport($startDate, $endDate), 'laporan-diskon.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.discounts', compact('discountUsages', 'start', 'end'));
                return $pdf->download('laporan-diskon.pdf');
            }
        }

        $labels = [];
        $dataUsages = [];
        $dataAmounts = [];
        foreach ($discountUsages as $du) {
            $labels[] = $du->discount ? $du->discount->name : 'Unknown';
            $dataUsages[] = $du->usage_count;
            $dataAmounts[] = $du->total_discount;
        }

        return view('admin.reports.discounts', compact('start', 'end', 'labels', 'dataUsages', 'dataAmounts', 'discountUsages'));
    }

    public function stocks(Request $request)
    {
        $start = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $stockMovements = \App\Models\OrderItem::whereHas('order', function($q) use($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('menu_id, SUM(quantity) as total_decreased')
            ->groupBy('menu_id')
            ->orderByDesc('total_decreased')
            ->with('menu')
            ->get();

        // Export Actions
        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StockExport($startDate, $endDate), 'laporan-pergerakan-stok.xlsx');
            } elseif ($request->export == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.stocks', compact('stockMovements', 'start', 'end'));
                return $pdf->download('laporan-pergerakan-stok.pdf');
            }
        }

        $labels = [];
        $data = [];
        foreach ($stockMovements as $sm) {
            $labels[] = $sm->menu ? $sm->menu->name : 'Unknown';
            $data[] = $sm->total_decreased;
        }

        return view('admin.reports.stocks', compact('start', 'end', 'labels', 'data', 'stockMovements'));
    }
}
