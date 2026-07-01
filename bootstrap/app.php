<?php

use App\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Use custom Application class that overrides phpBinary()
// to use wrapper script instead of PHP_BINARY (fixes spaces in path)
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdmin::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
            'function' => \App\Http\Middleware\CheckFunction::class,
            'function-access' => \App\Http\Middleware\FunctionAccess::class,
            'outbound-access' => \App\Http\Middleware\OutboundModuleAccess::class,
            'inbound-access' => \App\Http\Middleware\InboundModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
