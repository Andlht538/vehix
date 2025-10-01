<?php

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
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //

        $middleware->alias([
            'role'               => \App\Http\Middleware\CheckRole::class,
            'vehicle.validation' => \App\Http\Middleware\CheckVehiculeValidation::class,
            'prevent.blocked'    => \App\Http\Middleware\PreventBlockedUsers::class,
        ]);
    })

    ->withPolicies([
        \App\Models\Vehicle::class     => \App\Policies\VehiclePolicy::class,
        \App\Models\Insurance::class   => \App\Policies\InsurancePolicy::class,
        \App\Models\Repair::class      => \App\Policies\RepairPolicy::class,
        \App\Models\Maintenance::class => \App\Policies\MaintenancePolicy::class,
        \App\Models\Fueling::class     => \App\Policies\FuelingPolicy::class,
        \App\Models\Trip::class        => \App\Policies\TripPolicy::class,
        \App\Models\Receipt::class     => \App\Policies\ReceiptPolicy::class,
        \App\Models\Owner::class       => \App\Policies\OwnerPolicy::class,
    ])
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
