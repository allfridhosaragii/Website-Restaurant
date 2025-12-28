<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Reservation;
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

        // Order Stats (Simplified: Berhasil/Gagal)
        $statusStats = [
            'success' => $orders->whereIn('status', ['completed', 'processing', 'pending'])->count(),
            'failed' => $orders->where('status', 'cancelled')->count()
        ];

        // Reservation Stats
        $reservations = Reservation::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $reservationStats = [
            'success' => $reservations->whereIn('status', ['accepted', 'pending'])->count(),
            'failed' => $reservations->where('status', 'rejected')->count()
        ];

        // Monthly totals
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total');
        $totalOrders = $orders->count();

        // Generate month tabs (last 6 months)
        $monthTabs = [];
        for ($i = 11; $i >= 0; $i--) {
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

    public function api(Request $request)
    {
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
                ->select('order_items.*', 'menus.name as menu_name')
                ->get();
            $order->item_count = $order->items->sum('quantity');
            $order->formatted_total = 'Rp ' . number_format($order->total, 0, ',', '.');
            $order->formatted_date = Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i');
        }

        // Statistics for chart
        $statusStats = [
            'completed' => $orders->where('status', 'completed')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        // Payment stats
        $paymentStats = [
            'paid' => $orders->where('payment_status', 'paid')->count(),
            'unpaid' => $orders->where('payment_status', 'unpaid')->count(),
        ];

        // Reservation Stats
        $reservations = Reservation::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $reservationStats = [
            'success' => $reservations->whereIn('status', ['accepted', 'pending'])->count(),
            'failed' => $reservations->where('status', 'rejected')->count()
        ];

        // Monthly totals
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total');
        $totalOrders = $orders->count();

        return response()->json([
            'orders' => $orders,
            'statusStats' => $statusStats,
            'paymentStats' => $paymentStats,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'formattedRevenue' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'inProcess' => $statusStats['pending'] + $statusStats['processing'],
            'reservationStats' => $reservationStats,
        ]);
    }
}
