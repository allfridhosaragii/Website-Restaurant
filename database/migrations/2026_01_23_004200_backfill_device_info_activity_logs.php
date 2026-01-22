<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all DOWNLOAD_APK logs that don't have device info yet
        $logs = DB::table('activity_logs')
            ->where('action', 'DOWNLOAD_APK')
            ->where(function($q) {
                $q->whereNull('device_type')
                  ->orWhere('device_type', '');
            })
            ->get();

        foreach ($logs as $log) {
            $userAgent = $log->user_agent ?? '';
            
            // Parse User-Agent for device info
            $deviceType = 'Desktop';
            $deviceName = 'Unknown Device';
            $browser = 'Unknown';
            $os = 'Unknown';
            
            // Detect device type
            if (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent)) {
                $deviceType = 'Mobile';
            } elseif (preg_match('/iPad|Tablet/i', $userAgent)) {
                $deviceType = 'Tablet';
            }
            
            // Detect specific device name
            if (preg_match('/iPhone/', $userAgent)) {
                $deviceName = 'iPhone';
                if (preg_match('/iPhone OS (\d+)/', $userAgent, $m)) {
                    $deviceName = 'iPhone (iOS ' . $m[1] . ')';
                }
            } elseif (preg_match('/iPad/', $userAgent)) {
                $deviceName = 'iPad';
            } elseif (preg_match('/SM-[A-Z]\d+|Samsung|Galaxy/i', $userAgent)) {
                $deviceName = 'Samsung Galaxy';
            } elseif (preg_match('/Xiaomi|Redmi|POCO|Mi \d/i', $userAgent)) {
                $deviceName = 'Xiaomi/Redmi';
            } elseif (preg_match('/OPPO|CPH\d/i', $userAgent)) {
                $deviceName = 'OPPO';
            } elseif (preg_match('/Realme|RMX\d/i', $userAgent)) {
                $deviceName = 'Realme';
            } elseif (preg_match('/vivo/i', $userAgent)) {
                $deviceName = 'Vivo';
            } elseif (preg_match('/Huawei|Honor/i', $userAgent)) {
                $deviceName = 'Huawei/Honor';
            } elseif (preg_match('/OnePlus/i', $userAgent)) {
                $deviceName = 'OnePlus';
            } elseif (preg_match('/Pixel/i', $userAgent)) {
                $deviceName = 'Google Pixel';
            } elseif (preg_match('/Macintosh/', $userAgent)) {
                $deviceName = 'Mac';
            } elseif (preg_match('/Windows/', $userAgent)) {
                $deviceName = 'Windows PC';
            } elseif (preg_match('/Linux/', $userAgent) && !preg_match('/Android/', $userAgent)) {
                $deviceName = 'Linux PC';
            } elseif (preg_match('/Android/', $userAgent)) {
                $deviceName = 'Android Device';
            }
            
            // Detect browser
            if (preg_match('/Edg\/(\d+)/i', $userAgent, $m)) {
                $browser = 'Edge ' . $m[1];
            } elseif (preg_match('/OPR\/(\d+)/i', $userAgent, $m)) {
                $browser = 'Opera ' . $m[1];
            } elseif (preg_match('/Chrome\/(\d+)/i', $userAgent, $m)) {
                $browser = 'Chrome ' . $m[1];
            } elseif (preg_match('/Firefox\/(\d+)/i', $userAgent, $m)) {
                $browser = 'Firefox ' . $m[1];
            } elseif (preg_match('/Safari\/(\d+)/i', $userAgent) && preg_match('/Version\/(\d+)/i', $userAgent, $m)) {
                $browser = 'Safari ' . $m[1];
            }
            
            // Detect OS
            if (preg_match('/iPhone OS (\d+)[_.](\d+)/i', $userAgent, $m)) {
                $os = 'iOS ' . $m[1] . '.' . $m[2];
            } elseif (preg_match('/Android (\d+(\.\d+)?)/i', $userAgent, $m)) {
                $os = 'Android ' . $m[1];
            } elseif (preg_match('/Windows NT 10/i', $userAgent)) {
                $os = 'Windows 10/11';
            } elseif (preg_match('/Windows NT 6\.3/i', $userAgent)) {
                $os = 'Windows 8.1';
            } elseif (preg_match('/Windows NT 6\.1/i', $userAgent)) {
                $os = 'Windows 7';
            } elseif (preg_match('/Mac OS X (\d+)[_.](\d+)/i', $userAgent, $m)) {
                $os = 'macOS ' . $m[1] . '.' . $m[2];
            } elseif (preg_match('/Linux/i', $userAgent) && !preg_match('/Android/i', $userAgent)) {
                $os = 'Linux';
            }
            
            // Update the record
            DB::table('activity_logs')
                ->where('id', $log->id)
                ->update([
                    'device_type' => $deviceType,
                    'device_name' => $deviceName,
                    'browser' => $browser,
                    'os' => $os,
                ]);
        }
        
        echo "Updated " . count($logs) . " records\n";
    }

    public function down(): void
    {
        // Cannot undo this data migration
    }
};
