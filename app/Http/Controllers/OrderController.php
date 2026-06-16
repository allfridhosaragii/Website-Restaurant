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
            'payment_method' => 'nullable|string|in:gateway,deposit',
        ]);
        $userId = Auth::id();
        $user = Auth::user();
        $orderNumber = 'ORD-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $voucherCode = $request->input('voucher_code');
        
        try {
            $discountResult = \App\Services\DiscountService::applyDiscounts($request->items, $voucherCode, $user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        foreach ($request->items as $item) {
            $menu = DB::table('menus')->find($item['menu_id']);
            if ($menu && $menu->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi untuk menu: {$menu->name}. Sisa stok: {$menu->stock}"
                ], 400);
            }
        }

        $paymentMethod = $request->input('payment_method', 'gateway');
        $paymentStatus = 'pending';

        if ($paymentMethod === 'deposit') {
            if ($user->deposit_balance < $discountResult['total']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo deposit tidak mencukupi. Silakan pilih metode pembayaran lain atau top-up saldo.'
                ], 400);
            }
            // Deduct balance
            DB::table('users')->where('id', $userId)->decrement('deposit_balance', $discountResult['total']);
            
            // Log transaction
            DB::table('deposit_transactions')->insert([
                'user_id' => $userId,
                'type' => 'payment',
                'amount' => $discountResult['total'],
                'description' => 'Pembayaran pesanan ' . $orderNumber,
                'balance_after' => $user->deposit_balance - $discountResult['total'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $paymentStatus = 'paid';
        }

        $orderId = DB::table('orders')->insertGetId([
            'order_number' => $orderNumber,
            'user_id' => $userId,
            'type' => $request->type,
            'table_number' => $request->table_number,
            'subtotal' => $discountResult['subtotal_before_discount'],
            'discount_id' => $discountResult['discount_id'],
            'discount_amount' => $discountResult['discount_amount'],
            'subtotal_before_discount' => $discountResult['subtotal_before_discount'],
            'tax' => $discountResult['tax'],
            'total' => $discountResult['total'],
            'status' => 'pending',
            'payment_status' => $paymentStatus,
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($paymentMethod === 'deposit') {
            DB::table('order_payments')->insert([
                'order_id' => $orderId,
                'payment_method' => 'deposit',
                'amount' => $discountResult['total'],
                'paid_at' => now(),
                'processed_by' => clone $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Increment usage count for applied discounts
        foreach ($discountResult['applied_discounts'] as $appliedDisc) {
            DB::table('discounts')->where('id', $appliedDisc->id)->increment('usage_count');
            DB::table('discount_usages')->insert([
                'discount_id' => $appliedDisc->id,
                'user_id' => $userId,
                'order_id' => $orderId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($discountResult['orderItems'] as $item) {
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'menu_id' => $item['menu_id'],
                'menu_name' => $item['menu_name'],
                'price' => $item['price'], // Note: this is price after item discount
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'] ?? 0,
                'modifiers' => $item['modifiers'] ?? null,
                'is_promo' => $item['is_promo'] ?? false,
                'promo_name' => $item['promo_name'] ?? null,
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
            'total' => $discountResult['total'],
        ]);
    }
    public function show($id)
    {
        $userId = Auth::id();
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $userId)
        $order = \App\Models\Order::with(['items.menu', 'reviews', 'payments'])->where('user_id', auth()->id())->findOrFail($id);
        return view('customer.orders.show', compact('order'));
    }

    public function submitReview(Request $request, $id)
    {
        $order = \App\Models\Order::with('items')->where('user_id', auth()->id())->findOrFail($id);
        
        if ($order->status != 'completed') {
            return back()->with('error', 'Hanya pesanan yang sudah selesai yang bisa direview.');
        }

        if ($order->reviews()->exists()) {
            return back()->with('error', 'Anda sudah memberikan review untuk pesanan ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $menuIds = $order->items->pluck('menu_id')->unique();

        foreach ($menuIds as $menuId) {
            \App\Models\Review::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'menu_id' => $menuId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        }
        
        // Buat 1 record tanpa menu_id untuk rating keseluruhan pesanan
        \App\Models\Review::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Review Anda telah berhasil disimpan.');
    }
}