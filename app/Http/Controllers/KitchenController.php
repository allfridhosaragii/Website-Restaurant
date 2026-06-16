<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KitchenController extends Controller
{
    public function index()
    {
        return view('kitchen.index');
    }

    public function data(Request $request)
    {
        // Get active orders with their items
        $orders = Order::whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        $kdsData = [];

        foreach ($orders as $order) {
            $items = DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->where('order_items.status', 'active') // exclude voided items
                ->whereIn('order_items.kitchen_status', ['new', 'cooking', 'ready'])
                ->select(
                    'order_items.id',
                    'order_items.quantity',
                    'order_items.notes',
                    'order_items.modifiers',
                    'order_items.kitchen_status',
                    'order_items.cooking_started_at',
                    'order_items.ready_at',
                    'menus.name as menu_name'
                )
                ->get();

            if ($items->count() > 0) {
                // Calculate elapsed minutes since order placed
                $elapsedMinutes = Carbon::parse($order->created_at)->diffInMinutes(now());

                $kdsData[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'table_name' => $order->table ? 'Meja ' . $order->table->number : ($order->order_type == 'take_away' ? 'Take Away' : 'Unknown'),
                    'time_ordered' => Carbon::parse($order->created_at)->format('H:i'),
                    'elapsed_minutes' => $elapsedMinutes,
                    'items' => $items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'menu_name' => $item->menu_name,
                            'quantity' => $item->quantity,
                            'notes' => $item->notes,
                            'modifiers' => json_decode($item->modifiers, true) ?: [],
                            'kitchen_status' => $item->kitchen_status,
                            'cooking_started_at' => $item->cooking_started_at,
                        ];
                    })
                ];
            }
        }

        return response()->json($kdsData);
    }

    public function updateStatus(Request $request, $itemId)
    {
        $status = $request->input('status');
        
        $updateData = ['kitchen_status' => $status];
        
        if ($status === 'cooking') {
            $updateData['cooking_started_at'] = now();
            // Automatically update order status to processing if it's pending
            $item = DB::table('order_items')->where('id', $itemId)->first();
            if ($item) {
                Order::where('id', $item->order_id)
                     ->where('status', 'pending')
                     ->update(['status' => 'processing']);
            }
        } elseif ($status === 'ready') {
            $updateData['ready_at'] = now();
        } elseif ($status === 'served') {
            $updateData['served_at'] = now();
        }

        if (auth()->check()) {
            $updateData['cooked_by'] = auth()->id();
        }

        DB::table('order_items')
            ->where('id', $itemId)
            ->update($updateData);

        return response()->json(['success' => true]);
    }
}
