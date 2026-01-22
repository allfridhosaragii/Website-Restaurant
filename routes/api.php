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