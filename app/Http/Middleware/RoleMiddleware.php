<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin,manager')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Super Admin / is_admin can always pass
        if ($user->is_admin || $user->isSuperAdmin()) {
            return $next($request);
        }

        $userRole = $user->role ?? 'customer';

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Smart redirect based on role
        return match($userRole) {
            'waiter'   => redirect('/waiter')->with('error', 'Akses tidak diizinkan.'),
            'cashier'  => redirect('/admin/pos')->with('error', 'Akses tidak diizinkan.'),
            'customer' => redirect('/')->with('error', 'Akses tidak diizinkan.'),
            default    => abort(403, 'Unauthorized.'),
        };
    }
}
