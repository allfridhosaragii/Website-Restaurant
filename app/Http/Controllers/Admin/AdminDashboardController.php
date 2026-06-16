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
}