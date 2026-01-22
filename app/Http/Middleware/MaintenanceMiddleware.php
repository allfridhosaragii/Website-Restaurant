<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CmsSetting;
class MaintenanceMiddleware
{
    protected $excludedPaths = [
        'project',
        'project/*',
        'maintenance',
        'maintenance/*',
        'api/maintenance-status',
        'login',
        'logout',
        'register',
        'auth/*',
    ];
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('maintenance') || $request->is('maintenance/*')) {
            if (!auth()->check()) {
                return redirect('/login');
            }
            if (auth()->user()->email !== 'pedoprimasaragi@gmail.com') {
                return redirect('/');
            }
            return $next($request);
        }
        if ($this->isExcludedPath($request)) {
            return $next($request);
        }
        if ($this->isSuperAdmin()) {
            return $next($request);
        }
        if ($this->isMaintenanceMode()) {
            $path = $request->path();
            if ($path === '' || $path === '/') {
                return response()->view('maintenance', [], 503);
            }
            return redirect('/');
        }
        return $next($request);
    }
    protected function isExcludedPath(Request $request): bool
    {
        foreach ($this->excludedPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }
        return false;
    }
    protected function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->email === 'pedoprimasaragi@gmail.com';
    }
    protected function isMaintenanceMode(): bool
    {
        try {
            $setting = CmsSetting::where('key', 'maintenance_mode')->first();
            return $setting && $setting->value === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }
}