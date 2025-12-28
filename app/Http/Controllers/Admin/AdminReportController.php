<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        // Get current month and year from request or default to current
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get all orders for the selected month
        $orders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereMonth('orders.created_at', $month)
            ->whereYear('orders.created_at', $year)
            ->select(
                'orders.*',
                'users.name as customer_name',
                'users.email as customer_email'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        // Get order items for each order
        foreach ($orders as $order) {
            $order->items = DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menus.name as menu_name', 'menus.image_url')
                ->get();
            $order->item_count = $order->items->sum('quantity');
        }

        // Statistics for chart
        $statusStats = [
            'completed' => $orders->where('status', 'completed')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Payment stats for donut chart
        $paymentStats = [
            'paid' => $orders->where('payment_status', 'paid')->count(),
            'unpaid' => $orders->where('payment_status', 'unpaid')->count(),
        ];

        // Monthly totals
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total');
        $totalOrders = $orders->count();

        // Generate month tabs (last 6 months)
        $monthTabs = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthTabs[] = [
                'month' => $date->month,
                'year' => $date->year,
                'label' => $date->translatedFormat('M'),
                'active' => ($date->month == $month && $date->year == $year)
            ];
        }

        return view('admin.reports.index', compact(
            'orders',
            'statusStats',
            'paymentStats',
            'totalRevenue',
            'totalOrders',
            'monthTabs',
            'month',
            'year'
        ));
    }
}
