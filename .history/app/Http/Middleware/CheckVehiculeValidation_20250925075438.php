<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\VehiculeStatus;
use App\Enums\UserRole;

class CheckVehiculeValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Ce middleware s'applique seulement aux clients
        if ($user && $user->role === UserRole::CLIENT) {
            $pendingVehicule = $user->vehicules()
                ->where('status', VehiculeStatus::PENDING)
                ->first();

            if ($pendingVehicule && !$request->routeIs('dashboard', 'vehicules.*', 'logout')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
