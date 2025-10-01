<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vehicule;
use App\Models\Insurance;
use App\Models\Repair;
use App\Models\Maintenance;
use App\Models\Fueling;
use App\Models\Trip;
use App\Enums\UserRole;
use App\Enums\VehiculeStatus;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = $request->user();

        // Vérifier si l'utilisateur a un véhicule en attente
        $pendingVehicule = $user->vehicules()->where('status', VehiculeStatus::PENDING)->first();

        if ($pendingVehicule && $user->isClient()) {
            return Inertia::render('Dashboard/PendingValidation', [
                'vehicule' => $pendingVehicule,
            ]);
        }

        $dashboardData = $this->getDashboardData($user);

        return Inertia::render('Dashboard/Index', $dashboardData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }

    
}
