<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('is_admin', false)->count();
        $todayUsers = User::where('is_admin', false)
            ->whereDate('created_at', today())
            ->count();
        $last30DaysUsers = User::where('is_admin', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $totalMenus = DB::table('menus')->count();
        $totalOrders = DB::table('orders')->count();
        $todayOrders = DB::table('orders')->whereDate('created_at', today())->count();
        $pendingOrders = DB::table('orders')->where('status', 'pending')->count();
        $processingOrders = DB::table('orders')->where('status', 'processing')->count();
        $todayRevenue = DB::table('orders')
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
        $monthRevenue = DB::table('orders')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total');
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();
        $recentActivities = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(10)
            ->get();
        $todayRegistrations = User::where('is_admin', false)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $lowStockMenus = DB::table('menus')->whereRaw('stock <= min_stock')->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'todayUsers',
            'last30DaysUsers',
            'totalMenus',
            'totalOrders',
            'todayOrders',
            'pendingOrders',
            'processingOrders',
            'todayRevenue',
            'monthRevenue',
            'recentOrders',
            'recentActivities',
            'todayRegistrations',
            'lowStockMenus'
        ));
    }

    public function liveData(Request $request)
    {
        if (!auth()->user()->hasAdminPermission('dashboard_live')) {
            abort(403);
        }
        $period = $request->input('period', 'today'); // today, yesterday, 7days, 30days, custom
        
        $query = DB::table('orders')->where('payment_status', 'paid');
        $itemQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid');

        $now = now();
        $startDate = $now->startOfDay();
        $endDate = $now->copy()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = now()->subDay()->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                break;
            case '7days':
                $startDate = now()->subDays(7)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case '30days':
                $startDate = now()->subDays(30)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                    $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);
        $itemQuery->whereBetween('orders.created_at', [$startDate, $endDate]);

        // 1. Sales per Hour (if period is today or yesterday, show by hour, else by date)
        $salesData = [];
        if (in_array($period, ['today', 'yesterday']) || $startDate->diffInDays($endDate) <= 1) {
            $sales = clone $query;
            $salesResults = $sales->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total) as revenue'))
                ->groupBy('hour')
                ->get();
            for ($i = 0; $i < 24; $i++) {
                $salesData[sprintf('%02d:00', $i)] = 0;
            }
            foreach ($salesResults as $row) {
                $salesData[sprintf('%02d:00', $row->hour)] = (float) $row->revenue;
            }
        } else {
            $sales = clone $query;
            $salesResults = $sales->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
                ->groupBy('date')
                ->get();
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $salesData[$currentDate->format('Y-m-d')] = 0;
                $currentDate->addDay();
            }
            foreach ($salesResults as $row) {
                $salesData[$row->date] = (float) $row->revenue;
            }
        }

        // 2. Top 5 Menu Terlaris
        $topMenus = clone $itemQuery;
        $topMenusResults = $topMenus->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. Top Modifiers Paling Diminati
        $modifiersResults = clone $itemQuery;
        $itemsWithModifiers = $modifiersResults->whereNotNull('order_items.modifiers')->pluck('order_items.modifiers');
        
        $modifierCounts = [];
        foreach ($itemsWithModifiers as $json) {
            $mods = json_decode($json, true);
            if (is_array($mods)) {
                foreach ($mods as $mod) {
                    if (isset($mod['name'])) {
                        $name = $mod['name'];
                        if (!isset($modifierCounts[$name])) {
                            $modifierCounts[$name] = 0;
                        }
                        $modifierCounts[$name]++;
                    }
                }
            }
        }
        arsort($modifierCounts);
        $topModifiers = array_slice($modifierCounts, 0, 5, true);
        $topModifiersFormatted = [];
        foreach ($topModifiers as $name => $count) {
            $topModifiersFormatted[] = ['name' => $name, 'count' => $count];
        }

        // 4. Tabel: 10 Order Terbaru (live)
        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        // Additional Summary Stats
        $summary = [
            'total_revenue' => $query->sum('total'),
            'total_orders' => clone $query->count(),
            'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
        ];

        return response()->json([
            'salesChart' => [
                'labels' => array_keys($salesData),
                'data' => array_values($salesData),
            ],
            'topMenus' => $topMenusResults,
            'topModifiers' => $topModifiersFormatted,
            'recentOrders' => $recentOrders,
            'summary' => $summary
        ]);
    }
}