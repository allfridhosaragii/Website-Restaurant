<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class SuperAdminMiddleware
{
    protected $superAdminEmail = 'pedoprimasaragi@gmail.com';
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/');
        }
        if (auth()->user()->email !== $this->superAdminEmail) {
            return redirect('/');
        }
        return $next($request);
    }
}