<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\ViewerMiddleware::class,
            \App\Http\Middleware\MaintenanceMiddleware::class, // Global maintenance check
        ]);
        
        // Exclude visitor tracking API routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/maintenance-visitor/*',
            'api/site-visitor/*',
        ]);
        
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Automatically log all exceptions to the database
        $exceptions->report(function (\Throwable $e) {
            // Skip common/expected exceptions
            $skipTypes = [
                \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
                \Illuminate\Session\TokenMismatchException::class,
                \Illuminate\Auth\AuthenticationException::class,
                \Illuminate\Validation\ValidationException::class,
            ];
            
            if (!in_array(get_class($e), $skipTypes)) {
                try {
                    // \App\Models\ErrorLog::logException($e, request());
                } catch (\Exception $logError) {
                    // Fail silently
                }
            }
        });

        // Redirect 404 pages to landing page (only for web requests, not API)
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            return redirect('/');
        });
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');
    
    $directories = [
        '/tmp/storage/app',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

return $app;
