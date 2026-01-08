<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAdminReportController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Aggregate Stats
        $todayOrders = \DB::table('orders')->whereDate('created_at', today())->count();
        $totalOrders = \DB::table('orders')->count();
        $todayRevenue = \DB::table('orders')->whereDate('created_at', today())->sum('total');
        $totalRevenue = \DB::table('orders')->sum('total');
        
        $todayReservations = \DB::table('reservations')->whereDate('created_at', today())->count();
        $pendingReservations = \DB::table('reservations')->where('status', 'pending')->count();
        
        $activeUsers = \DB::table('users')->where('status', 'active')->count();
        $newUsers = \DB::table('users')->whereDate('created_at', today())->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'orders_today' => $todayOrders,
                'orders_total' => $totalOrders,
                'revenue_today' => $todayRevenue,
                'revenue_total' => $totalRevenue,
                'reservations_today' => $todayReservations,
                'reservations_pending' => $pendingReservations,
                'users_active' => $activeUsers,
                'users_new' => $newUsers
            ]
        ]);
    }
}
