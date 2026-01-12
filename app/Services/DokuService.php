<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DokuService
{
    private $clientId;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = env('DOKU_CLIENT_ID');
        $this->secretKey = env('DOKU_SECRET_KEY');
        $this->baseUrl = env('DOKU_IS_PRODUCTION', false) 
            ? 'https://api.doku.com' 
            : 'https://api-sandbox.doku.com';
    }

    public function generateSignature($requestId, $timestamp, $requestBody)
    {
        $digest = base64_encode(hash('sha256', $requestBody, true));
        $targetPath = '/checkout/v1/payment';
        
        $rawSignature = "Client-Id:" . $this->clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath . "\n" .
                        "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $this->secretKey, true));
        return 'HMACSHA256=' . $signature;
    }

    public function initiatePayment($order)
    {
        $requestId = 'REQ-' . time() . '-' . rand(1000, 9999);
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        
        // Ensure email is valid, default to a placeholder if missing (though user should have email)
        $email = auth()->user()->email ?? 'customer@example.com';
        $name = auth()->user()->name ?? 'Guest Customer';

        $data = [
            'order' => [
                'amount' => (int) $order->total, // Ensure integer
                'invoice_number' => $order->order_number,
                'currency' => 'IDR',
                'callback_url' => url('/payment/return'), // Redirect user here after payment
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 minutes
            ],
            'customer' => [
                'name' => $name,
                'email' => $email,
            ]
        ];

        $requestBody = json_encode($data);
        $signature = $this->generateSignature($requestId, $timestamp, $requestBody);

        try {
            Log::info('Initiating DOKU Payment for Order: ' . $order->order_number);
            
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/checkout/v1/payment', $data);

            if ($response->successful()) {
                Log::info('DOKU Payment Response: ' . $response->body());
                return $response->json();
            } else {
                Log::error('DOKU Payment Error: ' . $response->body());
                return ['error' => 'Payment gateway error: ' . $response->status() . ' - ' . $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('DOKU Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
