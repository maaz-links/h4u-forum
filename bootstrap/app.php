<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'reset.msglimit' => \App\Http\Middleware\ResetDailyMsgLimit::class,
            'check.banned' => \App\Http\Middleware\CheckBanned::class,
            'admin.perm' => \App\Http\Middleware\CheckAdminPermission::class,
        ]);
        // $middleware->alias([
            
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
