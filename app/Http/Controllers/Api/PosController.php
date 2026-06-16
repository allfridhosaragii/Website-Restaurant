<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    /**
     * Get all available menus for POS
     */
    public function getMenus()
    {
        $menus = \App\Models\Menu::with('modifiers.options')
                    ->where('is_available', true)
                    ->get();
                    
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    /**
     * Get all tables for POS
     */
    public function getTables()
    {
        $tables = DB::table('tables')->where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $tables
        ]);
    }

    /**
     * Get all customers for POS
     */
    public function getCustomers()
    {
        $customers = DB::table('users')
            ->where('is_admin', false)
            ->whereNull('role')
            ->select('id', 'name', 'email', 'deposit_balance')
            ->orderBy('name')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Checkout POS cart
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|integer|exists:users,id',
            'type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payments' => 'required|array|min:1|max:3',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference_number' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id(); // The cashier/admin ID

        DB::beginTransaction();

        try {
            $orderNumber = 'POS-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $voucherCode = $request->input('voucher_code');
            $customer = $request->customer_id ? \App\Models\User::find($request->customer_id) : null;
            
            $discountResult = \App\Services\DiscountService::applyDiscounts($request->items, $voucherCode, $customer);

            // Verify stock
            foreach ($request->items as $item) {
                $menu = DB::table('menus')->find($item['menu_id']);
                if ($menu) {
                    // Check stock if applicable
                    if (isset($menu->stock) && $menu->stock < $item['quantity']) {
                        throw new \Exception("Stok untuk {$menu->name} tidak cukup.");
                    }
                    
                    // Deduct stock
                    if (isset($menu->stock)) {
                        DB::table('menus')
                            ->where('id', $menu->id)
                            ->decrement('stock', $item['quantity']);
                    }
                }
            }

            $totalPayment = collect($request->payments)->sum('amount');
            if ($totalPayment < $discountResult['total']) {
                throw new \Exception("Total pembayaran (Rp " . number_format($totalPayment, 0, ',', '.') . ") kurang dari Grand Total (Rp " . number_format($discountResult['total'], 0, ',', '.') . ").");
            }

            $primaryPaymentMethod = count($request->payments) > 1 ? 'split' : $request->payments[0]['method'];

            $orderUserId = $request->customer_id ? $request->customer_id : $userId;

            // Check deposit balance if using deposit
            $totalDepositPayment = collect($request->payments)->where('method', 'deposit')->sum('amount');
            if ($totalDepositPayment > 0) {
                if (!$request->customer_id) {
                    throw new \Exception("Metode pembayaran Deposit memerlukan pemilihan pelanggan.");
                }
                $customer = DB::table('users')->where('id', $request->customer_id)->first();
                if (!$customer || $customer->deposit_balance < $totalDepositPayment) {
                    throw new \Exception("Saldo deposit pelanggan tidak mencukupi.");
                }
                
                // Deduct balance
                DB::table('users')->where('id', $request->customer_id)->decrement('deposit_balance', $totalDepositPayment);
                
                // Log transaction
                DB::table('deposit_transactions')->insert([
                    'user_id' => $request->customer_id,
                    'type' => 'payment',
                    'amount' => $totalDepositPayment,
                    'description' => 'Pembayaran pesanan POS ' . $orderNumber,
                    'balance_after' => $customer->deposit_balance - $totalDepositPayment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'user_id' => $orderUserId,
                'cashier_id' => $userId,
                'source' => 'pos',
                'type' => $request->type,
                'table_number' => $request->table_number,
                'subtotal' => $discountResult['subtotal_before_discount'],
                'discount_id' => $discountResult['discount_id'],
                'discount_amount' => $discountResult['discount_amount'],
                'subtotal_before_discount' => $discountResult['subtotal_before_discount'],
                'tax' => $discountResult['tax'],
                'total' => $discountResult['total'],
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $primaryPaymentMethod,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Payments
            foreach ($request->payments as $payment) {
                DB::table('order_payments')->insert([
                    'order_id' => $orderId,
                    'payment_method' => $payment['method'],
                    'amount' => $payment['amount'],
                    'reference_number' => $payment['reference_number'] ?? null,
                    'paid_at' => now(),
                    'processed_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Increment usage count for applied discounts
            foreach ($discountResult['applied_discounts'] as $appliedDisc) {
                DB::table('discounts')->where('id', $appliedDisc->id)->increment('usage_count');
                
                if ($orderUserId) {
                    DB::table('discount_usages')->insert([
                        'discount_id' => $appliedDisc->id,
                        'user_id' => $orderUserId,
                        'order_id' => $orderId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $userId,
                'action' => 'pos_checkout',
                'description' => "Kasir memproses pesanan POS {$orderNumber}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            // Fire event since POS orders are immediately completed
            event(new \App\Events\OrderCompleted($orderId));

            return response()->json([
                'success' => true,
                'message' => 'Pesanan POS berhasil diproses!',
                'order_number' => $orderNumber,
                'order_id' => $orderId,
                'total' => $discountResult['total'],
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
