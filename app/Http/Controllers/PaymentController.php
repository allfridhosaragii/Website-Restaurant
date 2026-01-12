<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DokuService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    public function pay($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('warning', 'Pesanan sudah dibayar.');
        }

        $response = $this->dokuService->initiatePayment($order);

        if (isset($response['error'])) {
            return redirect()->back()->with('error', 'Gagal memulai pembayaran: ' . $response['error']);
        }

        if (isset($response['response']['payment']['url'])) {
            return redirect($response['response']['payment']['url']);
        }

        return redirect()->back()->with('error', 'Respon tidak valid dari gateway pembayaran.');
    }

    public function return(Request $request)
    {
        // This is where the user is redirected after payment.
        // We can check the status from the query params or just thank them.
        // Usually DOKU appends `status` or similar.
        
        // For simplicity, we just redirect to the orders page with a message
        // The actual status update happens via Callback (Notification)
        
        return redirect('/customer/orders')->with('success', 'Pembayaran sedang diproses. Silakan cek status pesanan Anda secara berkala.');
    }

    public function callback(Request $request)
    {
        Log::info('DOKU Callback Received', $request->all());

        $headerSignature = $request->header('Signature');
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');
        $path = '/api/payment/notification'; // Must match exactly what Doku sends as Request-Target

        // Verify Signature (Optional but recommended - simplifying for now or implementing strict check)
        // To strictly verify:
         // $content = $request->getContent();
         // $signature = $this->dokuService->generateSignature($requestId, $timestamp, $content); 
         // if ($signature !== $headerSignature) { ... }

        // Logic to update order
        $orderNumber = $request->input('order.invoice_number');
        $transactionStatus = $request->input('transaction.status');

        if (!$orderNumber) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $order = DB::table('orders')->where('order_number', $orderNumber)->first();

        if (!$order) {
            Log::error('Order not found for callback: ' . $orderNumber);
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'SUCCESS') {
            DB::table('orders')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'status' => 'processing', // Automatically move to processing if paid
                'updated_at' => now()
            ]);
            
            // Add activity log
            DB::table('activity_logs')->insert([
                'user_id' => $order->user_id,
                'action' => 'payment_success',
                'description' => "Pembayaran berhasil untuk pesanan #" . $order->order_number,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

        } else if ($transactionStatus == 'FAILED') {
            DB::table('orders')->where('id', $order->id)->update([
                'payment_status' => 'failed',
                'updated_at' => now()
            ]);
        }

        return response()->json(['message' => 'Notification processed']);
    }
}
