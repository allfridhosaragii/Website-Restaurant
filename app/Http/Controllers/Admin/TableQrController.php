<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableQrController extends Controller
{
    /**
     * Generate or regenerate QR Code for a table.
     */
    public function generate($id)
    {
        $table = Table::findOrFail($id);
        
        $table->update([
            'qr_code_token' => Str::random(32),
            'qr_generated_at' => now(),
        ]);

        return back()->with('success', 'QR Code untuk Meja ' . $table->number . ' berhasil di-generate.');
    }

    /**
     * Download the QR Code as PNG.
     */
    public function download($id)
    {
        $table = Table::findOrFail($id);

        if (!$table->qr_code_token) {
            return back()->with('error', 'QR Code belum di-generate untuk meja ini.');
        }

        $url = url('/qr/' . $table->qr_code_token);
        
        // Generate a PNG image with 300px size and 10px margin
        $image = QrCode::format('png')
                    ->size(300)
                    ->margin(2)
                    ->generate($url);

        $filename = 'QR_Meja_' . $table->number . '.png';

        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
