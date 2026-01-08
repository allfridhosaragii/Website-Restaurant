<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class ApiAdminOrderController extends Controller
{
    public function index(Request $request)
    {
        // Ensure user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = \DB::table('orders')
            ->orderBy('created_at', 'desc')
            ->get();
            
        foreach ($orders as $order) {
            $order->items = \DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menus.name as menu_name', 'menus.image_url')
                ->get();
                
            $order->user = \DB::table('users')->where('id', $order->user_id)->first();
        }

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        \DB::table('orders')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Order status updated']);
    }
}
