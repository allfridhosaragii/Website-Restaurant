<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all DOWNLOAD_APK logs to refine their device names
        $logs = DB::table('activity_logs')
            ->where('action', 'DOWNLOAD_APK')
            ->whereNotNull('user_agent')
            ->get();

        foreach ($logs as $log) {
            $userAgent = $log->user_agent;
            $deviceName = $log->device_name; // Default to existing
            
            // Logic Deteksi Detail (Sama seperti di api.php)
            if (preg_match('/iPhone/', $userAgent)) {
                $deviceName = 'iPhone';
                if (preg_match('/iPhone\d+,\d+/', $userAgent, $m)) {
                    $deviceName = $m[0]; 
                }    
            } elseif (preg_match('/(Samsung|SM-[A-Z0-9]+)/i', $userAgent, $m)) {
                $model = $m[0];
                if (preg_match('/SM-S928/i', $userAgent)) $deviceName = 'Samsung S24 Ultra';
                elseif (preg_match('/SM-S921/i', $userAgent)) $deviceName = 'Samsung S24';
                elseif (preg_match('/SM-S918/i', $userAgent)) $deviceName = 'Samsung S23 Ultra';
                elseif (preg_match('/SM-S911/i', $userAgent)) $deviceName = 'Samsung S23';
                elseif (preg_match('/SM-S908/i', $userAgent)) $deviceName = 'Samsung S22 Ultra';
                elseif (preg_match('/SM-A5../i', $userAgent)) $deviceName = 'Samsung Galaxy A5x';
                elseif (preg_match('/SM-A3../i', $userAgent)) $deviceName = 'Samsung Galaxy A3x';
                else $deviceName = 'Samsung Device (' . $model . ')';
            } elseif (preg_match('/Pixel (\d+)/i', $userAgent, $m)) {
                $deviceName = 'Google Pixel ' . $m[1];
            } elseif (preg_match('/(Xiaomi|Redmi|POCO)\s?([A-Za-z0-9\s]+)/i', $userAgent, $m)) {
                $deviceName = $m[1] . ' ' . $m[2];
            } elseif (preg_match('/Build\/([A-Za-z0-9]+)/i', $userAgent, $m)) {
                $possibleModel = $m[1];
                if (strlen($possibleModel) > 3 && !preg_match('/(KTU|MRA|NRD|OPM|PPR)/', $possibleModel)) {
                     $deviceName = 'Android (' . $possibleModel . ')';
                }
            }
            
            // Refine desktop names
            if (preg_match('/Windows/', $userAgent)) $deviceName = 'Windows PC';
            if (preg_match('/Macintosh/', $userAgent)) $deviceName = 'MacBook / iMac';

            // Update record
            DB::table('activity_logs')
                ->where('id', $log->id)
                ->update(['device_name' => $deviceName]);
        }
    }

    public function down(): void
    {
    }
};
