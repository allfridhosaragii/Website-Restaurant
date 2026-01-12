<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('admin.orders.show', compact('order'));
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
            'user_id' => auth()->id(),
            'action' => 'update_order_status',
            'description' => "Mengubah status pesanan #{$order->order_number} menjadi {$request->status}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
        return back()->with('success', 'Status pesanan berhasil diperbarui!');
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
            'description' => "Menandai pesanan #{$order->order_number} sebagai LUNAS (manual)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pesanan berhasil ditandai sebagai LUNAS!');
    }
}