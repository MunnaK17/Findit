<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(__DIR__.'/../routes/channels.php')
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware custom di sini
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'throttle.reports' => \App\Http\Middleware\ThrottleReports::class,
            'throttle.claims' => \App\Http\Middleware\ThrottleClaims::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();