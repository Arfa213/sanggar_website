<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php', 
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies (NPM) so HTTPS routes generate correctly
        $middleware->trustProxies(at: '*');

        // Register alias for admin middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangani 419 Page Expired → redirect ke login dengan pesan ramah
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir atau tidak valid. Silakan masuk kembali.');
        });
    })->create();