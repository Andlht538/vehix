<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\VehiculeStatus;
use App\Enums\UserRole;
use App\Models\Vehicule;

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

        // This middleware only applies to clients
        if ($user && $user->role === UserRole::CLIENT) {
            
            // Check if user has a selected vehicle
            $selectedVehiculeId = session('selected_vehicule_id');
            
            if (!$selectedVehiculeId) {
                // No vehicle selected - redirect to selection
                return redirect()->route('vehicules.selection')
                    ->with('error', 'Veuillez sélectionner un véhicule validé pour accéder à cette section.');
            }

            // Verify the selected vehicle exists and is validated
            $selectedVehicule = Vehicule::find($selectedVehiculeId);
            
            if (!$selectedVehicule || 
                $selectedVehicule->user_id !== $user->id || 
                $selectedVehicule->status !== VehiculeStatus::VALIDATED) {
                
                // Selected vehicle is invalid - clear session and redirect
                session()->forget('selected_vehicule_id');
                
                return redirect()->route('vehicules.selection')
                    ->with('error', 'Le véhicule sélectionné n\'est pas valide. Veuillez en sélectionner un autre.');
            }
        }

        return $next($request);
    }
}