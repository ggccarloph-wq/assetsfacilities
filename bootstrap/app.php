<?php

require_once __DIR__.'/../app/Support/polyfills.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->trustProxies(at: '*');

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'approver' => \App\Http\Middleware\ApproverMiddleware::class,
            'supply_access' => \App\Http\Middleware\SupplyAccessMiddleware::class,
            'fmo_access' => \App\Http\Middleware\FmoAccessMiddleware::class,
            'fmo_super_admin' => \App\Http\Middleware\FmoSuperAdminMiddleware::class,
            'asset_management' => \App\Http\Middleware\AssetManagementMiddleware::class,
            'auth.jwt' => \App\Http\Middleware\JwtAuthMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();