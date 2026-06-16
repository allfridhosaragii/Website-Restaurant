<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\StatusController;
Route::get('/maintenance', function () {
    if (!auth()->check()) {
        return redirect('/login')->with('warning', 'Silakan login untuk mengakses halaman ini.');
    }
    if (auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
        return redirect('/');
    }
    return app(\App\Http\Controllers\StatusController::class)->index();
})->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::post('/maintenance/toggle', [\App\Http\Controllers\StatusController::class, 'toggle'])
    ->middleware(['auth', \App\Http\Middleware\SuperAdminMiddleware::class])
    ->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::get('/maintenance/preview', function () {
    if (!auth()->check() || auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
        abort(403);
    }
    return view('maintenance');
})->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::get('/api/maintenance-status', function () {
    $setting = \App\Models\CmsSetting::where('key', 'maintenance_mode')->first();
    $isMaintenanceMode = $setting && $setting->value === 'true';
    return response()->json([
        'maintenance' => $isMaintenanceMode
    ]);
});
Route::post('/api/maintenance-visitor/enter', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\MaintenanceVisitor::updateOrCreate(
        ['session_id' => $request->session_id],
        [
            'ip_address' => $request->ip(),
            'browser' => $request->browser,
            'browser_version' => $request->browser_version,
            'device_type' => $request->device_type,
            'operating_system' => $request->operating_system,
            'screen_resolution' => $request->screen_resolution,
            'entry_time' => now(),
            'last_heartbeat' => now(),
            'is_active' => true,
        ]
    );
    return response()->json(['success' => true, 'id' => $visitor->id]);
});
Route::post('/api/maintenance-visitor/heartbeat', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\MaintenanceVisitor::where('session_id', $request->session_id)->first();
    if ($visitor) {
        $visitor->last_heartbeat = now();
        $visitor->duration_seconds = $visitor->entry_time->diffInSeconds(now());
        $visitor->save();
    }
    return response()->json(['success' => true]);
});
Route::post('/api/maintenance-visitor/exit', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\MaintenanceVisitor::where('session_id', $request->session_id)->first();
    if ($visitor) {
        $visitor->exit_time = now();
        $visitor->is_active = false;
        $visitor->duration_seconds = $visitor->entry_time->diffInSeconds(now());
        $visitor->save();
    }
    return response()->json(['success' => true]);
});
Route::get('/api/maintenance-visitors', function () {
    if (!auth()->check() || auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
        abort(403);
    }
    \App\Models\MaintenanceVisitor::where('is_active', true)
        ->where('last_heartbeat', '<', now()->subSeconds(60))
        ->update(['is_active' => false, 'exit_time' => \DB::raw('last_heartbeat')]);
    $activeVisitors = \App\Models\MaintenanceVisitor::getActiveVisitors();
    $todayVisitors = \App\Models\MaintenanceVisitor::getTodayVisitors();
    return response()->json([
        'active' => $activeVisitors->map(fn($v) => [
            'id' => $v->id,
            'ip' => $v->ip_address,
            'browser' => $v->browser . ' ' . $v->browser_version,
            'device' => $v->device_type,
            'os' => $v->operating_system,
            'resolution' => $v->screen_resolution,
            'entry_time' => $v->entry_time->format('H:i:s'),
            'duration' => $v->formatted_duration,
            'duration_seconds' => $v->duration_seconds,
        ]),
        'total_today' => $todayVisitors->count(),
        'active_count' => $activeVisitors->count(),
    ]);
})->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::post('/api/site-visitor/enter', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\SiteVisitor::create([
        'session_id' => $request->session_id,
        'page_url' => $request->page_url,
        'page_title' => $request->page_title,
        'ip_address' => $request->ip(),
        'browser' => $request->browser,
        'browser_version' => $request->browser_version,
        'device_type' => $request->device_type,
        'operating_system' => $request->operating_system,
        'screen_resolution' => $request->screen_resolution,
        'user_id' => auth()->id(),
        'user_email' => auth()->user()?->email,
        'entry_time' => now(),
        'last_heartbeat' => now(),
        'is_active' => true,
    ]);
    return response()->json(['success' => true, 'id' => $visitor->id]);
});
Route::post('/api/site-visitor/heartbeat', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\SiteVisitor::where('session_id', $request->session_id)
        ->where('page_url', $request->page_url)
        ->where('is_active', true)
        ->first();
    if ($visitor) {
        $visitor->last_heartbeat = now();
        $visitor->duration_seconds = $visitor->entry_time->diffInSeconds(now());
        $visitor->save();
    }
    return response()->json(['success' => true]);
});
Route::post('/api/site-visitor/exit', function (\Illuminate\Http\Request $request) {
    $visitor = \App\Models\SiteVisitor::where('session_id', $request->session_id)
        ->where('page_url', $request->page_url)
        ->where('is_active', true)
        ->first();
    if ($visitor) {
        $visitor->exit_time = now();
        $visitor->is_active = false;
        $visitor->duration_seconds = $visitor->entry_time->diffInSeconds(now());
        $visitor->save();
    }
    return response()->json(['success' => true]);
});
Route::get('/api/site-visitors', function () {
    if (!auth()->check() || auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
        abort(403);
    }
    \App\Models\SiteVisitor::where('is_active', true)
        ->where('last_heartbeat', '<', now()->subSeconds(60))
        ->update(['is_active' => false, 'exit_time' => \DB::raw('last_heartbeat')]);
    $activeVisitors = \App\Models\SiteVisitor::getActiveVisitors();
    $todayVisitors = \App\Models\SiteVisitor::getTodayVisitors();
    return response()->json([
        'active' => $activeVisitors->map(fn($v) => [
            'id' => $v->id,
            'page_url' => $v->page_url,
            'page_title' => $v->page_title,
            'ip' => $v->ip_address,
            'browser' => $v->browser . ' ' . $v->browser_version,
            'device' => $v->device_type,
            'os' => $v->operating_system,
            'resolution' => $v->screen_resolution,
            'user_email' => $v->user_email,
            'entry_time' => $v->entry_time->format('H:i:s'),
            'duration_seconds' => $v->duration_seconds,
        ]),
        'total_today' => $todayVisitors->count(),
        'active_count' => $activeVisitors->count(),
    ]);
})->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::get('/api/site-visitors-history', function () {
    if (!auth()->check() || auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
        abort(403);
    }
    \App\Models\SiteVisitor::where('is_active', true)
        ->where('last_heartbeat', '<', now()->subSeconds(60))
        ->update(['is_active' => false, 'exit_time' => \DB::raw('last_heartbeat')]);
    $allVisitors = \App\Models\SiteVisitor::whereDate('entry_time', today())
        ->orderBy('entry_time', 'desc')
        ->get();
    $activeCount = $allVisitors->where('is_active', true)->count();
    return response()->json([
        'visitors' => $allVisitors->map(fn($v) => [
            'id' => $v->id,
            'page_url' => $v->page_url,
            'page_title' => $v->page_title,
            'ip' => $v->ip_address,
            'browser' => $v->browser . ' ' . $v->browser_version,
            'device' => $v->device_type,
            'os' => $v->operating_system,
            'resolution' => $v->screen_resolution,
            'user_email' => $v->user_email,
            'entry_time' => $v->entry_time->format('H:i:s'),
            'duration_seconds' => $v->duration_seconds,
            'is_active' => $v->is_active,
        ]),
        'total_today' => $allVisitors->count(),
        'active_count' => $activeCount,
    ]);
})->withoutMiddleware([\App\Http\Middleware\MaintenanceMiddleware::class]);
Route::get('/status', function () {
    return redirect('/maintenance');
});
Route::get('/offline', function () {
    return view('offline');
});
Route::middleware([\App\Http\Middleware\MaintenanceMiddleware::class])->group(function () {
    Route::get('/', function () {
        $featuredMenus = \DB::table('menus')->where('is_available', true)->limit(3)->get();
        return view('welcome', compact('featuredMenus'));
    });
Route::get('/menu', function () {
    $menus = \DB::table('menus')->where('is_available', true)->orderBy('category')->get();
    $favorites = [];
    if (auth()->check()) {
        $favorites = \App\Models\Favorite::where('user_id', auth()->id())->pluck('menu_id')->toArray();
    }
    return view('menu.index', compact('menus', 'favorites'));
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});

Route::get('/reservation', [ReservationController::class, 'create']);
Route::post('/reservation', [ReservationController::class, 'store'])->middleware('auth');
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::prefix('customer')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $userId = auth()->id();
        $orders = \DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        foreach ($orders as $order) {
            $order->items = \DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menus.image_url')
                ->get();
        }
        $totalOrders = \DB::table('orders')->where('user_id', $userId)->count();
        $totalReservations = \App\Models\Reservation::where('user_id', $userId)->count();
        $totalFavorites = \App\Models\Favorite::where('user_id', $userId)->count();
        $upcomingReservation = \App\Models\Reservation::where('user_id', $userId)
            ->where('date', '>=', now()->toDateString())
            ->whereIn('status', ['accepted', 'pending'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->first();
        return view('customer.dashboard', compact('orders', 'totalOrders', 'totalReservations', 'totalFavorites', 'upcomingReservation'));
    });
    Route::get('/orders', function () {
        $userId = auth()->id();
        $orders = \DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($orders as $order) {
            $order->items = \DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.*', 'menus.image_url')
                ->get();
        }
        return view('customer.orders.index', compact('orders'));
    });
    Route::get('/orders/create', function () {
        $menus = \DB::table('menus')->where('is_available', true)->orderBy('category')->get();
        return view('customer.orders.create', compact('menus'));
    });
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::get('/profile', function () {
        $user = auth()->user();
        $totalOrders = \DB::table('orders')->where('user_id', $user->id)->count();
        $totalReservations = \DB::table('reservations')->where('user_id', $user->id)->count();
        $points = $user->points;
        return view('customer.profile', compact('totalOrders', 'totalReservations', 'points'));
    });
    Route::get('/point', function () {
        $user = auth()->user();
        $points = $user->points;
        $history = \App\Models\PointTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'date' => $tx->created_at,
                    'description' => $tx->description,
                    'amount' => null,
                    'points_earned' => $tx->points,
                    'type' => $tx->type
                ];
            });
        $orderPoints = $history->where('type', 'order')->sum('points_earned');
        $reservationPoints = $history->where('type', 'reservation')->sum('points_earned');
        $totalOrders = $history->where('type', 'order')->count();
        $acceptedReservations = $history->where('type', 'reservation')->count();
        return view('customer.points', compact('points', 'history', 'orderPoints', 'reservationPoints', 'totalOrders', 'acceptedReservations'));
    });
    Route::get('/favorite', function () {
        $favorites = \App\Models\Favorite::where('user_id', auth()->id())
            ->with('menu') 
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.favorites', compact('favorites'));
    });
    Route::post('/favorite/{menuId}', function ($menuId) {
        $user = auth()->user();
        $favorite = \App\Models\Favorite::where('user_id', $user->id)
            ->where('menu_id', $menuId)
            ->first();
        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = 'Removed from favorites';
        } else {
            \App\Models\Favorite::create([
                'user_id' => $user->id,
                'menu_id' => $menuId
            ]);
            $status = 'added';
            $message = 'Added to favorites';
        }
        if (request()->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    });
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add']);
    Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'remove']);
    Route::put('/cart/{id}', [\App\Http\Controllers\CartController::class, 'update']);
    Route::get('/cart/count', [\App\Http\Controllers\CartController::class, 'count']);
    Route::delete('/cart', [\App\Http\Controllers\CartController::class, 'clear']);
    Route::get('/payment/return', [\App\Http\Controllers\PaymentController::class, 'return']);
    Route::get('/payment/{id}/pay', [\App\Http\Controllers\PaymentController::class, 'pay']);
});
Route::get('/dashboard', function () {
    if (auth()->check()) {
        return redirect('/customer/dashboard');
    }
    return redirect('/login');
});
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'index']);
    Route::put('/profile', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update']);
    Route::get('/menus', [AdminMenuController::class, 'index']);
    Route::get('/menus/create', [AdminMenuController::class, 'create']);
    Route::post('/menus', [AdminMenuController::class, 'store']);
    Route::get('/menus/{slug}/edit', [AdminMenuController::class, 'edit']);
    Route::put('/menus/{slug}', [AdminMenuController::class, 'update']);
    Route::delete('/menus/{slug}', [AdminMenuController::class, 'destroy']);
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/mark-paid', [AdminOrderController::class, 'markAsPaid']);
    Route::get('/reservations', [AdminReservationController::class, 'index']);
    Route::get('/reservations/{id}', [AdminReservationController::class, 'show']);
    Route::put('/reservations/{id}/status', [AdminReservationController::class, 'updateStatus']);
    Route::get('/activities', [AdminActivityController::class, 'index']);
    Route::get('/report/api', [\App\Http\Controllers\Admin\AdminReportController::class, 'api']);
    Route::get('/report', [\App\Http\Controllers\Admin\AdminReportController::class, 'index']);
    Route::get('/developer', [\App\Http\Controllers\Admin\AdminCmsController::class, 'index']);
    Route::get('/developer/pages', [\App\Http\Controllers\Admin\AdminCmsController::class, 'pages']);
    Route::get('/developer/pages/create', [\App\Http\Controllers\Admin\AdminCmsController::class, 'createPage']);
    Route::post('/developer/pages', [\App\Http\Controllers\Admin\AdminCmsController::class, 'storePage']);
    Route::get('/developer/pages/homepage/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editHomepage']);
    Route::post('/developer/pages/homepage', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateHomepage']);
    Route::get('/developer/pages/menu/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editMenuPage']);
    Route::post('/developer/pages/menu', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateMenuPage']);
    Route::get('/developer/pages/about/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editAboutPage']);
    Route::post('/developer/pages/about', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateAboutPage']);
    Route::get('/developer/pages/contact/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editContactPage']);
    Route::post('/developer/pages/contact', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateContactPage']);
    Route::get('/developer/pages/reservation/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editReservationPage']);
    Route::post('/developer/pages/reservation', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateReservationPage']);
    Route::get('/developer/pages/login/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editLoginPage']);
    Route::post('/developer/pages/login', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateLoginPage']);
    Route::get('/developer/pages/{id}/edit', [\App\Http\Controllers\Admin\AdminCmsController::class, 'editPage']);
    Route::put('/developer/pages/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updatePage']);
    Route::delete('/developer/pages/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'destroyPage']);
    Route::post('/developer/sections', [\App\Http\Controllers\Admin\AdminCmsController::class, 'storeSection']);
    Route::put('/developer/sections/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateSection']);
    Route::post('/developer/sections/reorder', [\App\Http\Controllers\Admin\AdminCmsController::class, 'reorderSections']);
    Route::delete('/developer/sections/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'destroySection']);
    Route::get('/developer/media', [\App\Http\Controllers\Admin\AdminCmsController::class, 'media']);
    Route::post('/developer/media', [\App\Http\Controllers\Admin\AdminCmsController::class, 'uploadMedia']);
    Route::delete('/developer/media/{id}', [\App\Http\Controllers\Admin\AdminCmsController::class, 'destroyMedia']);
    Route::get('/developer/settings', [\App\Http\Controllers\Admin\AdminCmsController::class, 'settings']);
    Route::post('/developer/settings', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateSettings']);
    Route::post('/developer/api/content', [\App\Http\Controllers\Admin\AdminCmsController::class, 'apiUpdateContent']);
    Route::post('/developer/api/image', [\App\Http\Controllers\Admin\AdminCmsController::class, 'apiUploadImage']);
    Route::post('/developer/settings', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateSettings']);
    Route::get('/application', [\App\Http\Controllers\Admin\AdminCmsController::class, 'application']);
    Route::post('/application', [\App\Http\Controllers\Admin\AdminCmsController::class, 'updateApplication']);
    Route::post('/application/generate-upload-url', [\App\Http\Controllers\Admin\AdminCmsController::class, 'generateUploadUrl']);
    Route::post('/application/finalize-upload', [\App\Http\Controllers\Admin\AdminCmsController::class, 'finalizeUpload']);
    Route::get('/application/api/downloads', [\App\Http\Controllers\Admin\AdminCmsController::class, 'getDownloadActivities']);
    Route::get('/inventory', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'index'])->name('admin.inventory.index');
    Route::post('/inventory/{id}/update', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'updateStock']);
    Route::post('/inventory/{id}/adjust', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'adjustStock']);
    Route::post('/inventory/{id}/toggle', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'toggleAvailability']);
    Route::post('/inventory/reset-all', [\App\Http\Controllers\Admin\AdminInventoryController::class, 'resetAllStock']);
    Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
        Route::get('/statistik', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'index'])->name('admin.statistics.index');
        Route::get('/statistik/api/activities', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'getActivities']);
        Route::get('/statistik/api/errors', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'getErrors']);
        Route::post('/statistik/api/errors/{id}/resolve', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'resolveError']);
        Route::get('/statistik/api/live-visitors', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'getLiveVisitors']);
        Route::get('/statistik/api/user-history', [\App\Http\Controllers\Admin\AdminStatisticsController::class, 'getUserHistory']);
    });
    Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
        Route::get('/access', [\App\Http\Controllers\Admin\AdminAccessController::class, 'index'])->name('admin.access.index');
        Route::get('/access/create', [\App\Http\Controllers\Admin\AdminAccessController::class, 'create'])->name('admin.access.create');
        Route::post('/access', [\App\Http\Controllers\Admin\AdminAccessController::class, 'store'])->name('admin.access.store');
        Route::get('/access/{id}/edit', [\App\Http\Controllers\Admin\AdminAccessController::class, 'edit'])->name('admin.access.edit');
        Route::put('/access/{id}', [\App\Http\Controllers\Admin\AdminAccessController::class, 'update'])->name('admin.access.update');
        Route::delete('/access/{id}', [\App\Http\Controllers\Admin\AdminAccessController::class, 'destroy'])->name('admin.access.destroy');
        Route::post('/access/{id}/toggle-super', [\App\Http\Controllers\Admin\AdminAccessController::class, 'toggleSuperAdmin'])->name('admin.access.toggle-super');
        Route::post('/access/{id}/toggle-online', [\App\Http\Controllers\Admin\AdminAccessController::class, 'toggleOnlineStatus'])->name('admin.access.toggle-online');
    });
});
Route::get('lang/{locale}', function ($locale) { 
    if (in_array($locale, ['en', 'id'])) { 
        session(['locale' => $locale]); 
    } 
    return redirect()->back(); 
})->name('lang.switch');

Route::get('/migrate-supabase', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return 'Migration ke Supabase berhasil! Silakan buka halaman utama.';
});
});