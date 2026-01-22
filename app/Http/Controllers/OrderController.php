<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);
        $userId = Auth::id();
        $orderNumber = 'ORD-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $subtotal = 0;
        $orderItems = [];
        foreach ($request->items as $item) {
            $menu = DB::table('menus')->find($item['menu_id']);
            if ($menu) {
                $itemSubtotal = $menu->price * $item['quantity'];
                $subtotal += $itemSubtotal;
                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }
        }
        $tax = $subtotal * 0.10;
        $total = $subtotal + $tax;
        $orderId = DB::table('orders')->insertGetId([
            'order_number' => $orderNumber,
            'user_id' => $userId,
            'type' => $request->type,
            'table_number' => $request->table_number,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        foreach ($orderItems as $item) {
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'menu_id' => $item['menu_id'],
                'menu_name' => $item['menu_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'action' => 'create_order',
            'description' => "Membuat pesanan baru 
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_number' => $orderNumber,
            'order_id' => $orderId,
            'total' => $total,
        ]);
    }
    public function show($id)
    {
        $userId = Auth::id();
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        if (!$order) {
            abort(404);
        }
        $order->items = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'menus.image_url')
            ->get();
        return view('customer.orders.show', compact('order'));
    }
}