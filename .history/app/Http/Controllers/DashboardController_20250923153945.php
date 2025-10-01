<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vehicule;
use App\Models\Assurance;
use App\Models\Reparation;
use App\Models\Maintenance;
use App\Models\Ravitaillement;
use App\Models\Trajet;
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

     private function getDashboardData($user)
    {
        $baseData = [
            'user' => $user->load('proprietaire'),
            'userRole' => $user->role->value,
        ];

        switch ($user->role) {
            case UserRole::CLIENT:
                return array_merge($baseData, $this->getClientDashboardData($user));

            case UserRole::VALIDATOR:
                return array_merge($baseData, $this->getValidatorDashboardData($user));

            case UserRole::ADMIN:
                return array_merge($baseData, $this->getAdminDashboardData($user));

            default:
                return $baseData;
        }
    }

    private function getClientDashboardData($user)
    {
        $vehicules = $user->vehicules()->with(['assurances', 'reparations', 'maintenances', 'ravitaillements'])->get();

        return [
            'stats' => [
                'totalVehicules' => $vehicules->count(),
                'validatedVehicules' => $vehicules->where('status', VehiculeStatus::VALIDATED)->count(),
                'pendingVehicules' => $vehicules->where('status', VehiculeStatus::PENDING)->count(),
                'rejectedVehicules' => $vehicules->where('status', VehiculeStatus::REJECTED)->count(),
                'totalReparations' => $user->reparations()->count(),
                'totalMaintenances' => $user->maintenances()->count(),
                'monthlyFuelCost' => $user->ravitaillements()->whereMonth('ravitaillement_date', now()->month)->sum('total_cost'),
                'totalTrips' => $user->trips()->count(),
            ],
            'recentActivities' => $this->getRecentActivities($user),
            'upcomingMaintenances' => $user->maintenances()
                ->whereNotNull('next_maintenance_date')
                ->where('next_maintenance_date', '<=', now()->addDays(30))
                ->with('vehicule')
                ->orderBy('next_maintenance_date')
                ->take(5)
                ->get(),
            'expiringAssurances' => $user->assurances()
                ->where('end_date', '<=', now()->addDays(30))
                ->with('vehicule')
                ->orderBy('end_date')
                ->take(5)
                ->get(),
        ];
    }

    private function getValidatorDashboardData($user)
    {
        $pendingVehicules = Vehicule::where('status', VehiculeStatus::PENDING)
            ->with(['user', 'assurances'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'stats' => [
                'pendingValidations' => $pendingVehicules->count(),
                'validatedToday' => Vehicule::where('validated_by', $user->id)
                    ->whereDate('validated_at', today())
                    ->count(),
                'totalValidated' => Vehicule::where('validated_by', $user->id)->count(),
                'rejectedToday' => Vehicule::where('validated_by', $user->id)
                    ->where('status', VehiculeStatus::REJECTED)
                    ->whereDate('validated_at', today())
                    ->count(),
            ],
            'pendingVehicules' => $pendingVehicules->take(10),
            'recentValidations' => Vehicule::where('validated_by', $user->id)
                ->with(['user'])
                ->orderBy('validated_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    private function getAdminDashboardData($user)
    {
        return [
            'stats' => [
                'totalUsers' => \App\Models\User::count(),
                'totalClients' => \App\Models\User::where('role', UserRole::CLIENT)->count(),
                'totalValidators' => \App\Models\User::where('role', UserRole::VALIDATOR)->count(),
                'totalVehicules' => Vehicule::count(),
                'pendingVehicules' => Vehicule::where('status', VehiculeStatus::PENDING)->count(),
                'validatedVehicules' => Vehicule::where('status', VehiculeStatus::VALIDATED)->count(),
                'rejectedVehicules' => Vehicule::where('status', VehiculeStatus::REJECTED)->count(),
                'newUsersThisMonth' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
            ],
            'recentUsers' => \App\Models\User::orderBy('created_at', 'desc')->take(10)->get(),
            'systemActivity' => $this->getSystemActivity(),
        ];
    }

    private function getRecentActivities($user)
    {
        $activities = collect();

        // Dernières réparations
        $reparations = $user->reparations()->with('vehicule')->orderBy('reparation_date', 'desc')->take(5)->get();
        foreach ($reparations as $reparation) {
            $activities->push([
                'type' => 'reparation',
                'date' => $reparation->reparation_date,
                'description' => "Réparation sur {$reparation->vehicule->full_name}",
                'amount' => $reparation->cost,
            ]);
        }

        // Dernières maintenances
        $maintenances = $user->maintenances()->with('vehicule')->orderBy('maintenance_date', 'desc')->take(5)->get();
        foreach ($maintenances as $maintenance) {
            $activities->push([
                'type' => 'maintenance',
                'date' => $maintenance->maintenance_date,
                'description' => "Maintenance ({$maintenance->type}) sur {$maintenance->vehicule->full_name}",
                'amount' => $maintenance->cost,
            ]);
        }

        // Derniers ravitaillements
        $ravitaillements = $user->ravitaillements()->with('vehicule')->orderBy('ravitaillement_date', 'desc')->take(5)->get();
        foreach ($ravitaillements as $ravitaillement) {
            $activities->push([
                'type' => 'ravitaillement',
                'date' => $ravitaillement->ravitaillement_date,
                'description' => "Ravitaillement {$ravitaillement->vehicule->full_name} ({$ravitaillement->liters}L)",
                'amount' => $ravitaillement->total_cost,
            ]);
        }

        return $activities->sortByDesc('date')->take(10)->values();
    }

    private function getSystemActivity()
    {
        return [
            'recentVehicules' => Vehicule::with(['user'])->orderBy('created_at', 'desc')->take(5)->get(),
            'recentValidations' => Vehicule::whereNotNull('validated_at')
                ->with(['user', 'validator'])
                ->orderBy('validated_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }
}
