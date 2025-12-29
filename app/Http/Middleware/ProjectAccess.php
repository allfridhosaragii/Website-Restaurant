<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class ProjectAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Auto-logout if admin is set to offline
        if ($user->isOffline()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Akun admin Anda telah di-set offline oleh Super Admin.');
        }

        // Super admin always has access
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if admin has 'project' permission
        if ($user->isAdmin() && $user->hasAdminPermission('project')) {
            return $next($request);
        }

        return redirect('/');
    }
}