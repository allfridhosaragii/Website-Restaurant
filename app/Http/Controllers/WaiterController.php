<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{
    public function index()
    {
        // For Waiter POS view
        return view('waiter.index');
    }

    public function myOrders()
    {
        $orders = \App\Models\Order::where('waiter_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('waiter.orders', compact('orders'));
    }

    public function getMenus()
    {
        $menus = \App\Models\Menu::with('modifiers.options')
                    ->where('is_available', true)
                    ->get();
        return response()->json(['success' => true, 'data' => $menus]);
    }

    public function getTables()
    {
        $tables = DB::table('tables')->where('is_active', true)->get();
        return response()->json(['success' => true, 'data' => $tables]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|integer|exists:users,id',
            'type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id(); // The waiter ID

        DB::beginTransaction();

        try {
            $orderNumber = 'ORD-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $voucherCode = $request->input('voucher_code');
            $customer = $request->customer_id ? \App\Models\User::find($request->customer_id) : null;
            
            $discountResult = \App\Services\DiscountService::applyDiscounts($request->items, $voucherCode, $customer);

            $orderUserId = $request->customer_id ? $request->customer_id : null;

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'user_id' => $orderUserId,
                'waiter_id' => $userId, // Waiter tracking
                'source' => 'pos', // or 'waiter'
                'type' => $request->type,
                'table_number' => $request->table_number,
                'subtotal' => $discountResult['subtotal_before_discount'],
                'discount_id' => $discountResult['discount_id'],
                'discount_amount' => $discountResult['discount_amount'],
                'subtotal_before_discount' => $discountResult['subtotal_before_discount'],
                'tax' => $discountResult['tax'],
                'total' => $discountResult['total'],
                'status' => 'pending', // Waiter orders are pending
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Increment usage count for applied discounts
            foreach ($discountResult['applied_discounts'] as $appliedDisc) {
                DB::table('discounts')->where('id', $appliedDisc->id)->increment('usage_count');
            }

            foreach ($discountResult['orderItems'] as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'menu_id' => $item['menu_id'],
                    'menu_name' => $item['menu_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'] ?? 0,
                    'modifiers' => $item['modifiers'] ?? null,
                    'is_promo' => $item['is_promo'] ?? false,
                    'promo_name' => $item['promo_name'] ?? null,
                    'kitchen_status' => 'new',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update table status if dine_in
            if ($request->type === 'dine_in' && $request->table_id) {
                DB::table('tables')->where('id', $request->table_id)->update(['status' => 'occupied']);
            }

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $userId,
                'action' => 'waiter_order',
                'description' => "Waiter menginput pesanan {$orderNumber}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diteruskan ke dapur!',
                'order_number' => $orderNumber,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
