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
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //

        $middleware->alias([
            'role'               => \App\Http\Middleware\CheckRole::class,
            'vehicule.validation' => \App\Http\Middleware\CheckVehiculeValidation::class,
            'prevent.blocked'    => \App\Http\Middleware\PreventBlockedUsers::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        
    })->create();

// --- AuthServiceProvider ---
if (class_exists(\App\Providers\AuthServiceProvider::class)) {
    $app->register(\App\Providers\AuthServiceProvider::class);
}

return $app;