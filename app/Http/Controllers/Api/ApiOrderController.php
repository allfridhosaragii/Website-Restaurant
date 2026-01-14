<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiOrderController extends Controller
{
    /**
     * List user's orders
     */
    public function index(Request $request)
    {
        $orders = \DB::table('orders')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($orders as $order) {
            $order->items = \DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menus.name as menu_name', 'menus.image_url')
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'total' => $order->total,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method ?? 'cash',
                    'notes' => $order->notes,
                    'items' => $order->items,
                    'item_count' => count($order->items),
                    'created_at' => $order->created_at,
                ];
            }),
        ]);
    }

    /**
     * Create new order from cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $user = $request->user();
        $cartItems = \App\Models\CartItem::where('user_id', $user->id)->with('menu')->get();
        
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 400);
        }
        
        $total = $cartItems->sum(function ($item) {
            return $item->menu ? $item->quantity * $item->menu->price : 0;
        });
        
        $orderId = (string) Str::uuid();
        
        \DB::table('orders')->insert([
            'id' => $orderId,
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        foreach ($cartItems as $item) {
            \DB::table('order_items')->insert([
                'id' => (string) Str::uuid(),
                'order_id' => $orderId,
                'menu_id' => $item->menu_id,
                'quantity' => $item->quantity,
                'price' => $item->menu->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Clear cart after order
        \App\Models\CartItem::where('user_id', $user->id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'order' => [
                'id' => $orderId,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ],
        ], 201);
    }

    /**
     * Get single order detail
     */
    public function show(Request $request, $id)
    {
        $order = \DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
        
        $order->items = \DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'menus.name as menu_name', 'menus.image_url')
            ->get();
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'total' => $order->total,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'notes' => $order->notes,
                'items' => $order->items,
                'created_at' => $order->created_at,
            ],
        ]);
    }
}
