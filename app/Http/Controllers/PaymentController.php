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
        return redirect('/customer/orders')->with('success', 'Pembayaran sedang diproses. Silakan cek status pesanan Anda secara berkala.');
    }
    public function callback(Request $request)
    {
        Log::channel('single')->info('=== DOKU CALLBACK START ===');
        Log::channel('single')->info('Headers: ' . json_encode($request->headers->all()));
        Log::channel('single')->info('Raw Body: ' . $request->getContent());
        Log::channel('single')->info('Parsed Body: ' . json_encode($request->all()));
        $data = $request->all();
        $orderNumber = null;
        $transactionStatus = null;
        if (isset($data['order']['invoice_number'])) {
            $orderNumber = $data['order']['invoice_number'];
        }
        elseif (isset($data['invoice_number'])) {
            $orderNumber = $data['invoice_number'];
        }
        elseif (isset($data['transaction']['invoice_number'])) {
            $orderNumber = $data['transaction']['invoice_number'];
        }
        if (isset($data['transaction']['status'])) {
            $transactionStatus = $data['transaction']['status'];
        } elseif (isset($data['status'])) {
            $transactionStatus = $data['status'];
        } elseif (isset($data['payment']['status'])) {
            $transactionStatus = $data['payment']['status'];
        }
        Log::channel('single')->info("Extracted - Order: {$orderNumber}, Status: {$transactionStatus}");
        if (!$orderNumber) {
            Log::channel('single')->error('No order number found in callback');
            return response()->json(['error' => 'Invalid data - no order number'], 400);
        }
        $order = DB::table('orders')->where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::channel('single')->error('Order not found: ' . $orderNumber);
            return response()->json(['error' => 'Order not found'], 404);
        }
        Log::channel('single')->info("Found order ID: {$order->id}, Current status: {$order->payment_status}");
        $successStatuses = ['SUCCESS', 'PAID', 'COMPLETED', 'success', 'paid', 'completed'];
        $failedStatuses = ['FAILED', 'EXPIRED', 'CANCELLED', 'failed', 'expired', 'cancelled'];
        if (in_array($transactionStatus, $successStatuses)) {
            DB::table('orders')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'updated_at' => now()
            ]);
            DB::table('activity_logs')->insert([
                'user_id' => $order->user_id,
                'action' => 'payment_success',
                'description' => "Pembayaran berhasil untuk pesanan {$orderNumber}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
            Log::channel('single')->info('Order marked as PAID successfully');
        } elseif (in_array($transactionStatus, $failedStatuses)) {
            DB::table('orders')->where('id', $order->id)->update([
                'payment_status' => 'failed',
                'updated_at' => now()
            ]);
            Log::channel('single')->info('Order marked as FAILED');
        } else {
            Log::channel('single')->warning("Unknown status: {$transactionStatus}");
        }
        Log::channel('single')->info('=== DOKU CALLBACK END ===');
        return response()->json(['message' => 'Notification processed']);
    }
}