<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableGroup;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminTableGroupController extends Controller
{
    public function merge(Request $request)
    {
        $request->validate([
            'table_ids' => 'required|array|min:2|max:5',
            'table_ids.*' => 'exists:tables,id'
        ]);

        $tableIds = $request->table_ids;
        
        // Ensure all tables are occupied
        $tables = Table::whereIn('id', $tableIds)->get();
        foreach ($tables as $table) {
            if ($table->status !== 'occupied') {
                return back()->with('error', "Meja {$table->number} tidak sedang occupied.");
            }
        }

        // Check if any order on these tables already belongs to an active group
        $orders = Order::whereIn('table_id', $tableIds)
            ->whereIn('status', ['pending', 'processing'])
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Tidak ada pesanan aktif di meja-meja yang dipilih.');
        }

        foreach ($orders as $order) {
            if ($order->table_group_id) {
                return back()->with('error', "Pesanan #{$order->order_number} sudah tergabung dalam grup lain.");
            }
        }

        DB::beginTransaction();
        try {
            $tableNames = $tables->pluck('number')->toArray();
            $groupName = 'Meja ' . implode('+', $tableNames);

            $group = TableGroup::create([
                'name' => $groupName,
                'table_ids' => $tableIds,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            foreach ($orders as $order) {
                $order->update(['table_group_id' => $group->id]);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'table_merged',
                'description' => "Menggabungkan " . count($tableIds) . " meja menjadi: {$groupName}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.pos.table-map')->with('success', "Berhasil membuat grup {$groupName}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menggabungkan meja: ' . $e->getMessage());
        }
    }

    public function unmerge(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if (!$order->table_group_id) {
            return back()->with('error', 'Pesanan ini tidak tergabung dalam grup.');
        }

        $group = TableGroup::findOrFail($order->table_group_id);

        DB::beginTransaction();
        try {
            $order->update(['table_group_id' => null]);

            // Check if group still has active orders
            $remainingOrders = Order::where('table_group_id', $group->id)
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            if ($remainingOrders == 0) {
                $group->update(['status' => 'closed']);
            }

            DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'table_unmerged',
                'description' => "Melepas Meja {$order->table_number} (Pesanan #{$order->order_number}) dari grup {$group->name}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', "Meja {$order->table_number} berhasil dilepas dari grup.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melepas meja: ' . $e->getMessage());
        }
    }
}
