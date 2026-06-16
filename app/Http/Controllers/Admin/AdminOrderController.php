<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name', 'users.email as customer_email');
        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('orders.order_number', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }
        $orders = $query->orderBy('orders.created_at', 'desc')->paginate(15);
        foreach ($orders as $order) {
            $order->item_count = DB::table('order_items')
                ->where('order_id', $order->id)
                ->sum('quantity');
        }
        $stats = [
            'total' => DB::table('orders')->count(),
            'pending' => DB::table('orders')->where('status', 'pending')->count(),
            'processing' => DB::table('orders')->where('status', 'processing')->count(),
            'completed' => DB::table('orders')->where('status', 'completed')->count(),
            'today_revenue' => DB::table('orders')
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];
        return view('admin.orders.index', compact('orders', 'stats'));
    }
    public function show($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.id', $id)
            ->select('orders.*', 'users.name as customer_name', 'users.email as customer_email', 'users.phone as customer_phone')
            ->first();
        if (!$order) {
            abort(404);
        }
        $order->items = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'menus.image_url')
            ->get();
            
        $hasPartialRefund = DB::table('refunds')
            ->where('order_id', $order->id)
            ->where('type', 'partial')
            ->where('status', 'completed')
            ->exists();
            
        $availableTables = \App\Models\Table::where('status', 'available')->orderBy('number')->get();
        
        $tableGroup = null;
        $groupOrders = [];
        if ($order->table_group_id) {
            $tableGroup = \App\Models\TableGroup::find($order->table_group_id);
            if ($tableGroup) {
                $groupOrders = DB::table('orders')
                    ->where('table_group_id', $tableGroup->id)
                    ->where('id', '!=', $order->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->get();
            }
        }
            
        return view('admin.orders.show', compact('order', 'hasPartialRefund', 'availableTables', 'tableGroup', 'groupOrders'));
    }
    public function kitchenPrint($id)
    {
        $order = \App\Models\Order::with('table')->findOrFail($id);
        
        $items = DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('order_items.order_id', $order->id)
            ->where('order_items.status', 'active')
            ->select('order_items.*', 'menus.name as menu_name', 'menus.station')
            ->get();

        // Group by station
        $stations = [
            'kitchen' => [],
            'bar' => [],
            'dessert' => []
        ];

        foreach ($items as $item) {
            $station = $item->station ?? 'kitchen';
            if ($station === 'all') $station = 'kitchen'; // fallback
            
            if (!isset($stations[$station])) {
                $stations[$station] = [];
            }
            $stations[$station][] = $item;
        }

        return view('admin.orders.kitchen_print', compact('order', 'stations'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            // Handle stock logic if status changed to 'completed'
            if ($request->status === 'completed' && $order->status !== 'completed') {
                $items = DB::table('order_items')->where('order_id', $id)->where('status', 'active')->get();
                foreach ($items as $item) {
                    $menu = DB::table('menus')->where('id', $item->menu_id)->lockForUpdate()->first();
                    if ($menu->stock < $item->quantity) {
                        DB::rollBack();
                        return back()->with('error', "Stok tidak mencukupi untuk menu: {$menu->name}. Sisa stok: {$menu->stock}");
                    }
                    
                    DB::table('menus')->where('id', $item->menu_id)->decrement('stock', $item->quantity);
                    
                    $updatedStock = $menu->stock - $item->quantity;
                    if ($updatedStock <= $menu->min_stock) {
                        DB::table('activity_logs')->insert([
                            'user_id' => auth()->id() ?? 1, // fallback to system or admin id if auth is somehow null
                            'action' => 'low_stock_alert',
                            'description' => "Stok menipis untuk menu: {$menu->name}. Sisa stok: {$updatedStock}",
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // Handle stock logic if status changed to 'cancelled' and was previously 'completed'
            if ($request->status === 'cancelled' && $order->status === 'completed') {
                $items = DB::table('order_items')->where('order_id', $id)->where('status', 'active')->get();
                foreach ($items as $item) {
                    DB::table('menus')->where('id', $item->menu_id)->increment('stock', $item->quantity);
                }
            }

            DB::table('orders')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            if ($request->status === 'completed') {
                DB::table('orders')->where('id', $id)->update([
                    'payment_status' => 'paid',
                ]);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id() ?? 1,
                'action' => 'update_order_status',
                'description' => "Mengubah status pesanan #{$order->order_number} menjadi {$request->status}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
            
            // Fire event if order completed
            if ($request->status === 'completed' && $order->status !== 'completed') {
                event(new \App\Events\OrderCompleted($order));
            }
            
            return back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
    public function markAsPaid(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan');
        }
        DB::table('orders')->where('id', $id)->update([
            'payment_status' => 'paid',
            'status' => $order->status === 'pending' ? 'processing' : $order->status,
            'updated_at' => now(),
        ]);
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'mark_order_paid',
            'description' => "Menandai pesanan 
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
        return back()->with('success', 'Pesanan berhasil ditandai sebagai LUNAS!');
    }

    public function splitUI($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.id', $id)
            ->select('orders.*', 'users.name as customer_name')
            ->first();

        if (!$order || $order->payment_status === 'paid' || in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan tidak ditemukan atau tidak dapat di-split (sudah selesai/dibatalkan).');
        }

        if ($order->parent_order_id !== null) {
            return back()->with('error', 'Bill hasil split tidak bisa di-split lagi.');
        }

        $order->items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->get();

        return view('admin.orders.split', compact('order'));
    }

    public function processSplit(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order || $order->payment_status === 'paid' || in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan tidak valid untuk di-split.');
        }

        if ($order->parent_order_id !== null) {
            return back()->with('error', 'Bill hasil split tidak bisa di-split lagi.');
        }

        $splitItems = $request->input('split_items');
        if (!$splitItems) {
            return back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $itemsToMove = [];
        $newSubtotal = 0;

        foreach ($splitItems as $itemId => $data) {
            if (isset($data['selected']) && $data['selected'] == '1') {
                $qty = (int)$data['quantity'];
                if ($qty > 0) {
                    $originalItem = DB::table('order_items')->where('id', $itemId)->where('order_id', $order->id)->first();
                    if ($originalItem && $qty <= $originalItem->quantity) {
                        $itemsToMove[] = [
                            'original' => $originalItem,
                            'move_qty' => $qty,
                            'price' => $originalItem->price,
                            'subtotal' => $qty * $originalItem->price
                        ];
                        $newSubtotal += ($qty * $originalItem->price);
                    }
                }
            }
        }

        if (empty($itemsToMove)) {
            return back()->with('error', 'Tidak ada item yang valid untuk dipindah.');
        }

        $newTax = round($newSubtotal * 0.10);
        $newTotal = $newSubtotal + $newTax;

        DB::beginTransaction();
        try {
            // Determine sequential order number for split
            $splitCount = DB::table('orders')->where('parent_order_id', $order->id)->count();
            $nextSplitNum = $splitCount + 1;
            
            // Create new order
            $newOrderNumber = $order->order_number . '-S-' . $nextSplitNum;
            $newOrderId = DB::table('orders')->insertGetId([
                'order_number' => $newOrderNumber,
                'user_id' => $order->user_id,
                'table_number' => $order->table_number,
                'type' => $order->type,
                'status' => $order->status,
                'payment_status' => 'pending',
                'subtotal' => $newSubtotal,
                'tax' => $newTax,
                'total' => $newTotal,
                'parent_order_id' => $order->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Move items
            $originalSubtotalReduction = 0;
            foreach ($itemsToMove as $move) {
                // Insert to new order
                DB::table('order_items')->insert([
                    'order_id' => $newOrderId,
                    'menu_id' => $move['original']->menu_id,
                    'menu_name' => $move['original']->menu_name,
                    'price' => $move['price'],
                    'quantity' => $move['move_qty'],
                    'subtotal' => $move['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $originalSubtotalReduction += $move['subtotal'];

                // Update original order
                $remainQty = $move['original']->quantity - $move['move_qty'];
                if ($remainQty > 0) {
                    DB::table('order_items')->where('id', $move['original']->id)->update([
                        'quantity' => $remainQty,
                        'subtotal' => $remainQty * $move['original']->price,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('order_items')->where('id', $move['original']->id)->delete();
                }
            }

            // Update original order totals
            $updatedSubtotal = $order->subtotal - $originalSubtotalReduction;
            $updatedTax = round($updatedSubtotal * 0.10);
            $updatedTotal = $updatedSubtotal + $updatedTax;

            DB::table('orders')->where('id', $order->id)->update([
                'subtotal' => $updatedSubtotal,
                'tax' => $updatedTax,
                'total' => $updatedTotal,
                'updated_at' => now(),
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'split_order',
                'description' => "Memecah pesanan #{$order->order_number} menjadi #{$newOrderNumber}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
            return redirect('/admin/orders')->with('success', "Split Bill berhasil! Bill baru: {$newOrderNumber}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses split bill: ' . $e->getMessage());
        }
    }

    public function voidItem(Request $request, $order_id, $item_id)
    {
        $request->validate([
            'manager_email' => 'required|email',
            'manager_password' => 'required',
            'void_reason' => 'required|string|max:255',
        ]);

        // Check auth for manager
        if (!\Auth::validate(['email' => $request->manager_email, 'password' => $request->manager_password])) {
            return back()->with('error', 'Otorisasi gagal! Email atau password manager salah.');
        }

        $manager = DB::table('users')->where('email', $request->manager_email)->first();
        if (!$manager || ($manager->role !== 'admin' && !$manager->is_admin)) {
            return back()->with('error', 'Otorisasi gagal! User tersebut tidak memiliki akses manager.');
        }

        $order = DB::table('orders')->where('id', $order_id)->first();
        if (!$order || in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Item tidak bisa di-void karena status order sudah selesai atau dibatalkan.');
        }

        $item = DB::table('order_items')->where('id', $item_id)->where('order_id', $order_id)->first();
        if (!$item || $item->status === 'voided') {
            return back()->with('error', 'Item tidak valid atau sudah di-void.');
        }

        DB::beginTransaction();
        try {
            DB::table('order_items')->where('id', $item_id)->update([
                'status' => 'voided',
                'void_reason' => $request->void_reason,
                'voided_by' => $manager->id,
                'voided_at' => now(),
            ]);

            // Note: Stok tidak dikembalikan di sini karena menurut logika saat ini, stok baru dikurangi saat status pesanan `completed`.
            // Jika logika berubah nanti di Fase 1.4, baris DB::table('menus')->where('id', $item->menu_id)->increment('stock', $item->quantity) bisa ditaruh di sini.

            // Recalculate original order
            $updatedSubtotal = $order->subtotal - $item->subtotal;
            $updatedTax = round($updatedSubtotal * 0.10);
            $updatedTotal = $updatedSubtotal + $updatedTax;

            // Check if all items are voided
            $activeItemsCount = DB::table('order_items')
                ->where('order_id', $order_id)
                ->where('status', 'active')
                ->count();

            $newStatus = $order->status;
            if ($activeItemsCount === 0) {
                $newStatus = 'cancelled';
            }

            DB::table('orders')->where('id', $order_id)->update([
                'status' => $newStatus,
                'subtotal' => $updatedSubtotal,
                'tax' => $updatedTax,
                'total' => $updatedTotal,
                'updated_at' => now(),
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => $manager->id,
                'action' => 'void_item',
                'description' => "Mem-void item {$item->menu_name} pada pesanan #{$order->order_number} dengan alasan: {$request->void_reason}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            if ($newStatus === 'cancelled') {
                DB::table('activity_logs')->insert([
                    'user_id' => $manager->id,
                    'action' => 'auto_cancel_order',
                    'description' => "Pesanan #{$order->order_number} otomatis dibatalkan karena semua item di-void.",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);
            }

            DB::commit();
            
            $msg = "Item {$item->menu_name} berhasil di-void.";
            if ($newStatus === 'cancelled') {
                $msg .= " Pesanan otomatis dibatalkan karena semua item di-void.";
            }
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses void item: ' . $e->getMessage());
        }
    }

    public function moveTable(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Hanya pesanan pending atau processing yang bisa dipindah mejanya.');
        }

        $validated = $request->validate([
            'to_table_id' => 'required|exists:tables,id',
            'reason' => 'nullable|string'
        ]);

        $toTable = \App\Models\Table::findOrFail($validated['to_table_id']);
        
        if ($toTable->status !== 'available') {
            return back()->with('error', 'Meja tujuan tidak tersedia (sedang ' . $toTable->status . ').');
        }

        $fromTableId = $order->table_id;
        $fromTableNumber = $order->table_number;

        if (!$fromTableId) {
            return back()->with('error', 'Pesanan ini tidak memiliki meja asal.');
        }

        DB::beginTransaction();
        try {
            // Update order table
            $order->table_id = $toTable->id;
            $order->table_number = $toTable->number;
            $order->save();

            // Record movement
            \App\Models\TableMovement::create([
                'order_id' => $order->id,
                'from_table_id' => $fromTableId,
                'to_table_id' => $toTable->id,
                'moved_by' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            // Update tables status
            $fromTable = \App\Models\Table::find($fromTableId);
            if ($fromTable) {
                // Check if any other active orders exist on the old table
                $otherActiveOrders = Order::where('table_id', $fromTable->id)
                    ->where('id', '!=', $order->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->exists();

                if (!$otherActiveOrders) {
                    $fromTable->status = 'available';
                    $fromTable->save();
                }
            }

            $toTable->status = 'occupied';
            $toTable->save();

            // Activity Log
            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'table_moved',
                'description' => "Memindah pesanan #{$order->order_number} dari Meja {$fromTableNumber} ke Meja {$toTable->number}. Alasan: " . ($validated['reason'] ?? '-'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.pos.table-map')->with('success', 'Pesanan berhasil dipindah ke Meja ' . $toTable->number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memindah meja: ' . $e->getMessage());
        }
    }
}