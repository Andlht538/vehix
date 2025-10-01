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

     private function getDashboardData($user)
    {
        $baseData = [
            'user' => $user->load('propri'),
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
        $vehicles = $user->vehicles()->with(['insurances', 'repairs', 'maintenances', 'fuelings'])->get();

        return [
            'stats' => [
                'totalVehicles' => $vehicles->count(),
                'validatedVehicles' => $vehicles->where('status', VehicleStatus::VALIDATED)->count(),
                'pendingVehicles' => $vehicles->where('status', VehicleStatus::PENDING)->count(),
                'rejectedVehicles' => $vehicles->where('status', VehicleStatus::REJECTED)->count(),
                'totalRepairs' => $user->repairs()->count(),
                'totalMaintenances' => $user->maintenances()->count(),
                'monthlyFuelCost' => $user->fuelings()->whereMonth('fueling_date', now()->month)->sum('total_cost'),
                'totalTrips' => $user->trips()->count(),
            ],
            'recentActivities' => $this->getRecentActivities($user),
            'upcomingMaintenances' => $user->maintenances()
                ->whereNotNull('next_maintenance_date')
                ->where('next_maintenance_date', '<=', now()->addDays(30))
                ->with('vehicle')
                ->orderBy('next_maintenance_date')
                ->take(5)
                ->get(),
            'expiringInsurances' => $user->insurances()
                ->where('end_date', '<=', now()->addDays(30))
                ->with('vehicle')
                ->orderBy('end_date')
                ->take(5)
                ->get(),
        ];
    }

    private function getValidatorDashboardData($user)
    {
        $pendingVehicles = Vehicle::where('status', VehicleStatus::PENDING)
            ->with(['user', 'insurances'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'stats' => [
                'pendingValidations' => $pendingVehicles->count(),
                'validatedToday' => Vehicle::where('validated_by', $user->id)
                    ->whereDate('validated_at', today())
                    ->count(),
                'totalValidated' => Vehicle::where('validated_by', $user->id)->count(),
                'rejectedToday' => Vehicle::where('validated_by', $user->id)
                    ->where('status', VehicleStatus::REJECTED)
                    ->whereDate('validated_at', today())
                    ->count(),
            ],
            'pendingVehicles' => $pendingVehicles->take(10),
            'recentValidations' => Vehicle::where('validated_by', $user->id)
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
                'totalVehicles' => Vehicle::count(),
                'pendingVehicles' => Vehicle::where('status', VehicleStatus::PENDING)->count(),
                'validatedVehicles' => Vehicle::where('status', VehicleStatus::VALIDATED)->count(),
                'rejectedVehicles' => Vehicle::where('status', VehicleStatus::REJECTED)->count(),
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
        $repairs = $user->repairs()->with('vehicle')->orderBy('repair_date', 'desc')->take(5)->get();
        foreach ($repairs as $repair) {
            $activities->push([
                'type' => 'repair',
                'date' => $repair->repair_date,
                'description' => "Réparation sur {$repair->vehicle->full_name}",
                'amount' => $repair->cost,
            ]);
        }

        // Dernières maintenances
        $maintenances = $user->maintenances()->with('vehicle')->orderBy('maintenance_date', 'desc')->take(5)->get();
        foreach ($maintenances as $maintenance) {
            $activities->push([
                'type' => 'maintenance',
                'date' => $maintenance->maintenance_date,
                'description' => "Maintenance ({$maintenance->type}) sur {$maintenance->vehicle->full_name}",
                'amount' => $maintenance->cost,
            ]);
        }

        // Derniers ravitaillements
        $fuelings = $user->fuelings()->with('vehicle')->orderBy('fueling_date', 'desc')->take(5)->get();
        foreach ($fuelings as $fueling) {
            $activities->push([
                'type' => 'fueling',
                'date' => $fueling->fueling_date,
                'description' => "Ravitaillement {$fueling->vehicle->full_name} ({$fueling->liters}L)",
                'amount' => $fueling->total_cost,
            ]);
        }

        return $activities->sortByDesc('date')->take(10)->values();
    }

    private function getSystemActivity()
    {
        return [
            'recentVehicles' => Vehicle::with(['user'])->orderBy('created_at', 'desc')->take(5)->get(),
            'recentValidations' => Vehicle::whereNotNull('validated_at')
                ->with(['user', 'validator'])
                ->orderBy('validated_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }
}
