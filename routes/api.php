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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes for Culinaire Restaurant Mobile App
| All routes are prefixed with /api
| Version: 1.0.1 (Force Deploy)
|
*/

// Public Routes (No Authentication Required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/google', [ApiAuthController::class, 'googleAuth']);
});

// Payment Notification (DOKU Callback)
Route::post('/payment/notification', [\App\Http\Controllers\PaymentController::class, 'callback']);

// Public Menu Routes
Route::get('/menus', [ApiMenuController::class, 'index']);
Route::get('/menus/{slug}', [ApiMenuController::class, 'show']);

// Public Table Routes (for reservation display)
Route::get('/tables', [ApiTableController::class, 'index']);

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/user', [ApiAuthController::class, 'user']);
        Route::put('/user', [ApiAuthController::class, 'updateProfile']);
    });
    
    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [ApiCartController::class, 'index']);
        Route::post('/', [ApiCartController::class, 'store']);
        Route::put('/{id}', [ApiCartController::class, 'update']);
        Route::delete('/{id}', [ApiCartController::class, 'destroy']);
        Route::delete('/', [ApiCartController::class, 'clear']);
    });
    
    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('/{id}', [ApiOrderController::class, 'show']);
    });
    
    // Reservations
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ApiReservationController::class, 'index']);
        Route::post('/', [ApiReservationController::class, 'store']);
        Route::get('/{id}', [ApiReservationController::class, 'show']);
        Route::post('/{id}/upload-proof', [ApiReservationController::class, 'uploadProof']);
    });
    
    // Tables (availability check still requires auth)
    Route::get('/tables/{id}/availability', [ApiTableController::class, 'checkAvailability']);
    
    // Favorites
    Route::prefix('favorites')->group(function () {
        Route::get('/', [ApiFavoriteController::class, 'index']);
        Route::post('/{menuId}', [ApiFavoriteController::class, 'toggle']);
    });
    
    // Dashboard Stats
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'total_orders' => \DB::table('orders')->where('user_id', $user->id)->count(),
            'total_reservations' => \DB::table('reservations')->where('user_id', $user->id)->count(),
            'total_favorites' => \App\Models\Favorite::where('user_id', $user->id)->count(),
            'points' => $user->points, // Use real points from database
        ]);
    });

    // Points & Rewards
    Route::get('/points', [ApiPointController::class, 'index']);

    // ---------------------------------------------------
    // SUPER APP ADMIN API ROUTES
    // ---------------------------------------------------
    Route::prefix('admin')->group(function () {
        // Dashboard / Reports
        Route::get('/reports', [ApiAdminReportController::class, 'index']);
        
        // Orders Management
        Route::get('/orders', [ApiAdminOrderController::class, 'index']);
        Route::put('/orders/{id}/status', [ApiAdminOrderController::class, 'updateStatus']);
        
        // Reservations Management
        Route::get('/reservations', [ApiAdminReservationController::class, 'index']);
        Route::put('/reservations/{id}/status', [ApiAdminReservationController::class, 'updateStatus']);
        
        // User Management
        Route::get('/users', [ApiAdminUserController::class, 'index']);
        Route::put('/users/{id}/status', [ApiAdminUserController::class, 'updateStatus']);
        
        // Menu Management
        Route::get('/menus', [ApiAdminMenuController::class, 'index']);
        Route::post('/menus', [ApiAdminMenuController::class, 'store']);
        Route::put('/menus/{slug}', [ApiAdminMenuController::class, 'update']);
        Route::delete('/menus/{slug}', [ApiAdminMenuController::class, 'destroy']);

        // CMS & Settings
        Route::get('/cms', [ApiAdminCmsController::class, 'index']);
        Route::post('/cms/maintenance', [ApiAdminCmsController::class, 'toggleMaintenance']);
    });
});
