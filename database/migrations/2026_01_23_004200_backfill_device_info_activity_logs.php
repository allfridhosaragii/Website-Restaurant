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
            
            // Detect specifics
            if (preg_match('/iPhone/', $userAgent)) {
                $deviceName = 'iPhone';
                // iPhone usually doesn't expose model in UA, but we can try
                if (preg_match('/iPhone\d+,\d+/', $userAgent, $m)) {
                    $deviceName = $m[0]; // e.g. iPhone13,3
                }    
            } elseif (preg_match('/iPad/', $userAgent)) {
                $deviceName = 'iPad';
            } elseif (preg_match('/(Samsung|SM-[A-Z0-9]+)/i', $userAgent, $m)) {
                $model = $m[0];
                // Generic Samsung Mapping (Simple regex for common flagships)
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
                // Fallback: use Build ID as hint, often contains model
                $possibleModel = $m[1];
                if (strlen($possibleModel) > 3 && !preg_match('/(KTU|MRA|NRD|OPM|PPR)/', $possibleModel)) {
                     $deviceName = 'Android (' . $possibleModel . ')';
                }
            }
            
            // Desktop handling
            if ($deviceType === 'Desktop') {
                if (preg_match('/Windows/', $userAgent)) $deviceName = 'Windows PC';
                if (preg_match('/Macintosh/', $userAgent)) $deviceName = 'MacBook / iMac';
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
