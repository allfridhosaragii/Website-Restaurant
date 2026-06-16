<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminRefundController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('refunds')
            ->join('orders', 'refunds.order_id', '=', 'orders.id')
            ->join('users as kasir', 'refunds.requested_by', '=', 'kasir.id')
            ->leftJoin('users as manager', 'refunds.approved_by', '=', 'manager.id')
            ->select('refunds.*', 'orders.order_number', 'kasir.name as kasir_name', 'manager.name as manager_name');

        if ($request->filled('status')) {
            $query->where('refunds.status', $request->status);
        }

        $refunds = $query->orderBy('refunds.created_at', 'desc')->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function create($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.id', $id)
            ->select('orders.*', 'users.name as customer_name')
            ->first();

        if (!$order || $order->status !== 'completed') {
            return back()->with('error', 'Pesanan tidak ditemukan atau belum selesai.');
        }

        $order->items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->where('status', 'active')
            ->get();

        return view('admin.orders.refund', compact('order'));
    }

    public function store(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (!$order || $order->status !== 'completed') {
            return back()->with('error', 'Pesanan tidak valid untuk direfund.');
        }

        $request->validate([
            'reason' => 'required|string',
            'refund_items' => 'required|array',
            'manager_email' => 'nullable|email',
            'manager_password' => 'nullable|string',
        ]);

        $manager = null;
        if ($request->filled('manager_email') && $request->filled('manager_password')) {
            if (!Auth::validate(['email' => $request->manager_email, 'password' => $request->manager_password])) {
                return back()->with('error', 'Otorisasi gagal! Email atau password manager salah.');
            }

            $manager = DB::table('users')->where('email', $request->manager_email)->first();
            if (!$manager || ($manager->role !== 'admin' && !$manager->is_admin)) {
                return back()->with('error', 'Otorisasi gagal! User tersebut tidak memiliki akses manager.');
            }
        }

        $refundItems = $request->input('refund_items');
        $itemsToRefund = [];
        $totalRefundAmount = 0;
        $activeItemCount = DB::table('order_items')->where('order_id', $order->id)->where('status', 'active')->count();
        $refundedItemCount = 0;

        foreach ($refundItems as $itemId => $data) {
            if (isset($data['selected']) && $data['selected'] == '1') {
                $qty = (int)$data['quantity'];
                if ($qty > 0) {
                    $originalItem = DB::table('order_items')->where('id', $itemId)->where('order_id', $order->id)->where('status', 'active')->first();
                    // We also need to check how many have been refunded previously, but for simplicity assuming we can refund up to original qty
                    if ($originalItem && $qty <= $originalItem->quantity) {
                        $subtotal = $qty * $originalItem->price;
                        $itemsToRefund[] = [
                            'original' => $originalItem,
                            'refund_qty' => $qty,
                            'price' => $originalItem->price,
                            'subtotal' => $subtotal
                        ];
                        $totalRefundAmount += $subtotal;
                        
                        if ($qty == $originalItem->quantity) {
                            $refundedItemCount++;
                        }
                    }
                }
            }
        }

        if (empty($itemsToRefund)) {
            return back()->with('error', 'Tidak ada item yang valid untuk di-refund.');
        }

        // Add 10% tax to the refund amount
        $totalRefundAmount += round($totalRefundAmount * 0.10);

        // Determine if it's full or partial
        $type = ($refundedItemCount == $activeItemCount) ? 'full' : 'partial';

        DB::beginTransaction();
        try {
            $status = $manager ? 'completed' : 'pending';
            $refundId = DB::table('refunds')->insertGetId([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'type' => $type,
                'amount' => $totalRefundAmount,
                'reason' => $request->reason,
                'status' => $status,
                'requested_by' => auth()->id(),
                'approved_by' => $manager ? $manager->id : null,
                'approved_at' => $manager ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($itemsToRefund as $move) {
                DB::table('refund_items')->insert([
                    'refund_id' => $refundId,
                    'order_item_id' => $move['original']->id,
                    'quantity' => $move['refund_qty'],
                    'price' => $move['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // If completed, return stock (Fase 1.4 logic forward-compatible)
                if ($status === 'completed') {
                    DB::table('menus')->where('id', $move['original']->menu_id)->increment('stock', $move['refund_qty']);
                }
            }

            if ($status === 'completed' && $type === 'full') {
                DB::table('orders')->where('id', $order->id)->update([
                    'status' => 'refunded',
                    'updated_at' => now(),
                ]);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'refund_requested',
                'description' => "Mengajukan refund pesanan #{$order->order_number} sebesar Rp " . number_format($totalRefundAmount, 0, ',', '.'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
            
            if ($status === 'completed') {
                return redirect('/admin/orders/' . $order->id)->with('success', 'Refund otomatis disetujui karena diotorisasi Manager.');
            } else {
                return redirect('/admin/orders/' . $order->id)->with('success', 'Pengajuan Refund berhasil dikirim dan menunggu persetujuan Manager.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        $refund = DB::table('refunds')->where('id', $id)->first();
        if (!$refund || $refund->status !== 'pending') {
            return back()->with('error', 'Refund tidak valid atau sudah diproses.');
        }

        DB::beginTransaction();
        try {
            DB::table('refunds')->where('id', $id)->update([
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'updated_at' => now(),
            ]);

            $refundItems = DB::table('refund_items')
                ->join('order_items', 'refund_items.order_item_id', '=', 'order_items.id')
                ->where('refund_id', $id)
                ->select('refund_items.*', 'order_items.menu_id')
                ->get();

            foreach ($refundItems as $item) {
                DB::table('menus')->where('id', $item->menu_id)->increment('stock', $item->quantity);
            }

            if ($refund->type === 'full') {
                DB::table('orders')->where('id', $refund->order_id)->update([
                    'status' => 'refunded',
                    'updated_at' => now(),
                ]);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'refund_approved',
                'description' => "Menyetujui refund ID #{$id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Refund berhasil disetujui dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui refund: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $refund = DB::table('refunds')->where('id', $id)->first();
        if (!$refund || $refund->status !== 'pending') {
            return back()->with('error', 'Refund tidak valid atau sudah diproses.');
        }

        DB::table('refunds')->where('id', $id)->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'updated_at' => now(),
        ]);

        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'refund_rejected',
            'description' => "Menolak refund ID #{$id} dengan alasan: {$request->rejection_reason}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Refund berhasil ditolak.');
    }
}
