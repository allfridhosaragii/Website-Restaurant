<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !in_array(auth()->user()->role, ['cashier', 'waiter', 'manager', 'viewer']))) {
            abort(403, 'Unauthorized. Admin/Staff access required.');
        }
        if (auth()->user()->isOffline()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->with('error', 'Akun admin Anda telah di-set offline oleh Super Admin.');
        }
        return $next($request);
    }
}