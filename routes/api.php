<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiMenuController;
use App\Http\Controllers\Api\ApiCartController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiReservationController;
use App\Http\Controllers\Api\ApiFavoriteController;
use App\Http\Controllers\Api\ApiTableController;
use App\Http\Controllers\Api\Admin\ApiAdminOrderController;
use App\Http\Controllers\Api\Admin\ApiAdminReservationController;
use App\Http\Controllers\Api\Admin\ApiAdminUserController;
use App\Http\Controllers\Api\Admin\ApiAdminMenuController;
use App\Http\Controllers\Api\Admin\ApiAdminReportController;
use App\Http\Controllers\Api\Admin\ApiAdminCmsController;
use App\Http\Controllers\Api\PosController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/google', [ApiAuthController::class, 'googleAuth']);
});
Route::post('/payment/notification', [\App\Http\Controllers\PaymentController::class, 'callback']);
Route::post('/error-report', function (Request $request) {
    try {
        $data = [
            'type' => $request->type ?? 'JavaScript Error',
            'message' => $request->message ?? 'Unknown error',
            'file' => $request->file,
            'line' => $request->line,
            'trace' => $request->stack,
            'url' => $request->url,
            'method' => 'GET',
            'ip_address' => $request->ip(),
            'user_id' => auth()->id(),
            'user_agent' => $request->userAgent(),
            'browser' => $request->browser,
            'device_type' => $request->deviceType,
            'screen_size' => $request->screenSize,
        ];
        if ($request->screenshot) {
            $supabaseUrl = env('SUPABASE_URL');
            $supabaseKey = env('SUPABASE_SERVICE_ROLE_KEY'); 
            $bucket = env('SUPABASE_BUCKET');
            if ($supabaseUrl && $supabaseKey && $bucket) {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->screenshot);
                $imageData = base64_decode($imageData);
                $filename = 'error-screenshots/' . date('Y-m-d') . '/error_' . time() . '_' . uniqid() . '.png';
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $supabaseKey,
                    'Content-Type' => 'image/png',
                ])->withBody($imageData, 'image/png')
                  ->post("{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}");
                if ($response->successful()) {
                    $data['screenshot_url'] = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
                }
            }
        }
        \App\Models\ErrorLog::create($data);
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Error report failed: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
});
Route::post('/app-download-log', function (Request $request) {
    try {
        $userAgent = $request->userAgent() ?? '';
        
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
        
        // Get user_id from request body (sent from frontend) or try auth
        $userId = $request->user_id ?: (auth('sanctum')->id() ?: (auth()->id() ?: null));
        
        \DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'action' => 'DOWNLOAD_APK',
            'description' => 'Mendownload file: ' . ($request->filename ?? 'Culinaire.apk'),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'browser' => $browser,
            'os' => $os,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});

Route::get('/menus', [ApiMenuController::class, 'index']);
Route::get('/menus/{slug}', [ApiMenuController::class, 'show']);
Route::get('/tables', [ApiTableController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/user', [ApiAuthController::class, 'user']);
        Route::put('/user', [ApiAuthController::class, 'updateProfile']);
    });
    Route::prefix('cart')->group(function () {
        Route::get('/', [ApiCartController::class, 'index']);
        Route::post('/', [ApiCartController::class, 'store']);
        Route::put('/{id}', [ApiCartController::class, 'update']);
        Route::delete('/{id}', [ApiCartController::class, 'destroy']);
        Route::delete('/', [ApiCartController::class, 'clear']);
    });
    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('/{id}', [ApiOrderController::class, 'show']);
    });
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ApiReservationController::class, 'index']);
        Route::post('/', [ApiReservationController::class, 'store']);
        Route::get('/{id}', [ApiReservationController::class, 'show']);
        Route::post('/{id}/upload-proof', [ApiReservationController::class, 'uploadProof']);
    });
    Route::get('/tables/{id}/availability', [ApiTableController::class, 'checkAvailability']);
    Route::prefix('favorites')->group(function () {
        Route::get('/', [ApiFavoriteController::class, 'index']);
        Route::post('/{menuId}', [ApiFavoriteController::class, 'toggle']);
    });
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'total_orders' => \DB::table('orders')->where('user_id', $user->id)->count(),
            'total_reservations' => \DB::table('reservations')->where('user_id', $user->id)->count(),
            'total_favorites' => \App\Models\Favorite::where('user_id', $user->id)->count(),
            'points' => $user->points, 
        ]);
    });
    Route::get('/points', [ApiPointController::class, 'index']);
    Route::prefix('admin')->group(function () {
        Route::get('/reports', [ApiAdminReportController::class, 'index']);
        Route::get('/orders', [ApiAdminOrderController::class, 'index']);
        Route::put('/orders/{id}/status', [ApiAdminOrderController::class, 'updateStatus']);
        Route::get('/reservations', [ApiAdminReservationController::class, 'index']);
        Route::put('/reservations/{id}/status', [ApiAdminReservationController::class, 'updateStatus']);
        Route::get('/users', [ApiAdminUserController::class, 'index']);
        Route::put('/users/{id}/status', [ApiAdminUserController::class, 'updateStatus']);
        Route::get('/menus', [ApiAdminMenuController::class, 'index']);
        Route::post('/menus', [ApiAdminMenuController::class, 'store']);
        Route::put('/menus/{slug}', [ApiAdminMenuController::class, 'update']);
        Route::delete('/menus/{slug}', [ApiAdminMenuController::class, 'destroy']);
        Route::get('/cms', [ApiAdminCmsController::class, 'index']);
        Route::post('/cms/maintenance', [ApiAdminCmsController::class, 'toggleMaintenance']);
    });
});


Route::get('/migrate-supabase', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return 'Migration ke Supabase berhasil! Silakan buka halaman utama.';
});