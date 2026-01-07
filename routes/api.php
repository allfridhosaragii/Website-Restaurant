<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiMenuController;
use App\Http\Controllers\Api\ApiCartController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiReservationController;
use App\Http\Controllers\Api\ApiFavoriteController;

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

// Public Menu Routes
Route::get('/menus', [ApiMenuController::class, 'index']);
Route::get('/menus/{slug}', [ApiMenuController::class, 'show']);

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
    });
    
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
            'points' => \DB::table('orders')->where('user_id', $user->id)->count() * 1000 
                      + \DB::table('reservations')
                            ->where('user_id', $user->id)
                            ->whereIn('status', ['accepted', 'completed'])
                            ->count() * 10000,
        ]);
    });
});
