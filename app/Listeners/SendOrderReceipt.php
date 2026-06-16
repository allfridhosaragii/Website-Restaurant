<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceiptMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SendOrderReceipt implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    private function getOrderData($id)
    {
        $order = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.id', $id)
            ->select(
                'orders.*',
                'users.name as customer_name',
                'users.email as customer_email',
                'users.phone as customer_phone'
            )
            ->first();

        if (!$order) {
            return null;
        }

        $order->items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->get();

        return $order;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        try {
            // Cek apakah data order merupakan object StdClass atau Model
            $orderId = is_numeric($event->order) ? $event->order : $event->order->id;
            $order = $this->getOrderData($orderId);

            if (!$order) {
                return;
            }

            if (empty($order->customer_email)) {
                return; // Tidak ada email, tidak bisa kirim
            }

            $pdf = Pdf::loadView('receipts.pdf', compact('order'))->setPaper([0, 0, 226.77, 800], 'portrait');
            $pdfData = $pdf->output();

            Mail::to($order->customer_email)->send(new OrderReceiptMail($order, $pdfData));
            
            DB::table('orders')->where('id', $order->id)->update([
                'receipt_sent_at' => now(),
                'receipt_sent_via' => 'email_auto',
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => $order->user_id, // Atribusikan ke user yang pesan (karena auto)
                'action' => 'receipt_sent_auto',
                'description' => "Struk otomatis terkirim via Email untuk pesanan {$order->order_number}",
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System Auto',
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-send receipt email for order ' . ($event->order->id ?? 'unknown') . ': ' . $e->getMessage());
        }
    }
}
