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
        $menus = DB::table('menus')->where('is_available', true)->get();
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
     * Checkout POS cart
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id(); // The cashier/admin ID

        DB::beginTransaction();

        try {
            $orderNumber = 'POS-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $menu = DB::table('menus')->find($item['menu_id']);
                
                if ($menu) {
                    // Check stock if applicable
                    if (isset($menu->stock) && $menu->stock < $item['quantity']) {
                        throw new \Exception("Stok untuk {$menu->name} tidak cukup.");
                    }

                    $itemSubtotal = $menu->price * $item['quantity'];
                    $subtotal += $itemSubtotal;
                    
                    $orderItems[] = [
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'price' => $menu->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                    ];

                    // Deduct stock
                    if (isset($menu->stock)) {
                        DB::table('menus')
                            ->where('id', $menu->id)
                            ->decrement('stock', $item['quantity']);
                    }
                }
            }

            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'user_id' => $userId, // the user who ordered, wait, in POS the cashier is creating it for an anonymous user or walking customer. 
                // We'll set user_id to cashier_id or null. Since user_id is foreignId constrained, it might be required. Let's set it to cashier for now or create a guest user id.
                // Wait, orders migration says `user_id` constrained(). Let's check if it's nullable.
                'cashier_id' => $userId,
                'source' => 'pos',
                'type' => $request->type,
                'table_number' => $request->table_number,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'completed', // POS is typically completed directly
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
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

            return response()->json([
                'success' => true,
                'message' => 'Pesanan POS berhasil diproses!',
                'order_number' => $orderNumber,
                'order_id' => $orderId,
                'total' => $total,
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
