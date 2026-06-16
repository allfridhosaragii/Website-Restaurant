<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceiptMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
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
            abort(404);
        }

        $order->items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->get();

        return $order;
    }

    public function print($id)
    {
        $order = $this->getOrderData($id);
        
        if (!in_array($order->status, ['completed', 'refunded'])) {
            return redirect()->back()->with('error', 'Struk hanya bisa dicetak untuk pesanan yang sudah selesai atau direfund.');
        }

        DB::table('orders')->where('id', $id)->update([
            'receipt_sent_at' => now(),
            'receipt_sent_via' => 'print',
        ]);

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'receipt_printed',
            'description' => "Mencetak struk untuk pesanan {$order->order_number}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return view('receipts.print', compact('order'));
    }

    public function sendWhatsapp($id)
    {
        $order = $this->getOrderData($id);
        
        if (!in_array($order->status, ['completed', 'refunded'])) {
            return redirect()->back()->with('error', 'Struk hanya bisa dikirim untuk pesanan yang sudah selesai atau direfund.');
        }

        if (empty($order->customer_phone)) {
            return redirect()->back()->with('error', 'Pelanggan tidak memiliki nomor telepon.');
        }

        DB::table('orders')->where('id', $id)->update([
            'receipt_sent_at' => now(),
            'receipt_sent_via' => 'whatsapp',
        ]);

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'receipt_sent_whatsapp',
            'description' => "Mengirim struk via WhatsApp untuk pesanan {$order->order_number}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $text = "Halo {$order->customer_name},\n";
        $text .= "Berikut adalah struk pesanan Anda:\n\n";
        $text .= "No Order: {$order->order_number}\n";
        $text .= "Tanggal: " . date('d M Y H:i', strtotime($order->created_at)) . "\n";
        $text .= "--------------------------------\n";
        
        foreach ($order->items as $item) {
            $text .= "- {$item->quantity}x {$item->menu_name}\n";
            $mods = $item->modifiers ? json_decode($item->modifiers, true) : null;
            if ($mods && is_array($mods)) {
                foreach ($mods as $mod) {
                    $text .= "  + {$mod['name']}: {$mod['option_name']}\n";
                }
            }
        }
        
        $text .= "--------------------------------\n";
        $subtotal = $order->subtotal_before_discount ?? $order->subtotal;
        $text .= "Subtotal: Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        if ($order->discount_amount > 0) {
            $text .= "Diskon: -Rp " . number_format($order->discount_amount, 0, ',', '.') . "\n";
        }
        $text .= "Pajak (10%): Rp " . number_format($order->tax, 0, ',', '.') . "\n";
        $text .= "Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\n";
        $text .= "Terima kasih telah berbelanja di tempat kami!";

        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        $url = "https://wa.me/{$phone}?text=" . urlencode($text);

        return redirect($url);
    }

    public function sendEmail($id)
    {
        $order = $this->getOrderData($id);
        
        if (!in_array($order->status, ['completed', 'refunded'])) {
            return redirect()->back()->with('error', 'Struk hanya bisa dikirim untuk pesanan yang sudah selesai atau direfund.');
        }

        if (empty($order->customer_email)) {
            return redirect()->back()->with('error', 'Pelanggan tidak memiliki alamat email.');
        }

        $pdf = Pdf::loadView('receipts.pdf', compact('order'))->setPaper([0, 0, 226.77, 800], 'portrait');
        $pdfData = $pdf->output();

        try {
            Mail::to($order->customer_email)->send(new OrderReceiptMail($order, $pdfData));
            
            DB::table('orders')->where('id', $id)->update([
                'receipt_sent_at' => now(),
                'receipt_sent_via' => 'email',
            ]);

            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'receipt_sent_email',
                'description' => "Mengirim struk via Email untuk pesanan {$order->order_number}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Struk berhasil dikirim ke email pelanggan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
