<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class DisplayController extends Controller
{
    public function index()
    {
        return view('display.index');
    }

    public function data(Request $request)
    {
        // Get all active orders that have at least one 'ready' or 'served' item in the last few minutes
        $orders = Order::whereIn('status', ['pending', 'processing'])
            ->get();

        $readyOrders = [];
        $servedOrders = [];

        foreach ($orders as $order) {
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->where('status', 'active')
                ->whereIn('kitchen_status', ['ready', 'served'])
                ->get();

            if ($items->count() > 0) {
                // If all active items in the order are 'served', or if some are served and we just want to show recent served
                // Actually, let's categorize the whole order:
                // If order has 'ready' items, it's Ready
                $hasReady = $items->contains('kitchen_status', 'ready');
                $hasServed = $items->contains('kitchen_status', 'served');

                $orderData = [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'table_name' => $order->table ? 'Meja ' . $order->table->number : ($order->order_type == 'take_away' ? 'Take Away' : 'Bungkus')
                ];

                if ($hasReady) {
                    $readyOrders[] = $orderData;
                } elseif ($hasServed) {
                    // Check if it was served recently (e.g., within the last 5 minutes) to show in "Served" column before disappearing
                    $latestServedTime = $items->where('kitchen_status', 'served')->max('served_at');
                    if ($latestServedTime && Carbon::parse($latestServedTime)->diffInMinutes(now()) <= 5) {
                        $servedOrders[] = $orderData;
                    }
                }
            }
        }

        return response()->json([
            'ready' => $readyOrders,
            'served' => $servedOrders
        ]);
    }
}
