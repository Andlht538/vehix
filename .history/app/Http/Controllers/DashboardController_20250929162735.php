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
    public function index(Request $request)
    {
        //
        $user = $request->user();

       // Handle client redirection logic
        if ($user->isClient()) {
            return $this->handleClientDashboard($user);
        }

        // For validators and admins, show their respective dashboards
        $dashboardData = $this->getDashboardData($user);
        return Inertia::render('Dashboard/Index', $dashboardData);
    }

    /**
     * Handle client dashboard access with redirect logic
     */
    private function handleClientDashboard($user)
    {
        $vehiculeCount = $user->vehicules()->count();

        // Case 1: No vehicles at all - redirect to create
        if ($vehiculeCount === 0) {
            return redirect()->route('vehicules.create')
                ->with('info', 'Veuillez ajouter votre premier véhicule pour commencer.');
        }

        // Case 2: Has vehicles but none selected - redirect to selection
        $selectedVehiculeId = session('selected_vehicule_id');
        
        if (!$selectedVehiculeId) {
            return redirect()->route('vehicules.selection')
                ->with('info', 'Veuillez sélectionner un véhicule pour continuer.');
        }

        // Case 3: Selected vehicle exists and is validated
        $selectedVehicule = Vehicule::find($selectedVehiculeId);
        
        if (!$selectedVehicule || $selectedVehicule->user_id !== $user->id) {
            // Selected vehicle doesn't exist or doesn't belong to user
            session()->forget('selected_vehicule_id');
            return redirect()->route('vehicules.selection')
                ->with('error', 'Le véhicule sélectionné n\'est plus disponible.');
        }

        if ($selectedVehicule->status !== VehiculeStatus::VALIDATED) {
            // Selected vehicle is not validated
            session()->forget('selected_vehicule_id');
            return redirect()->route('vehicules.selection')
                ->with('error', 'Le véhicule sélectionné n\'est pas validé.');
        }

        // Case 4: All good - show dashboard with selected vehicle
        $dashboardData = $this->getClientDashboardData($user, $selectedVehicule);
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

    /**
     * Get dashboard data based on user role
     */

     private function getDashboardData($user)
    {
        $baseData = [
            'user' => $user->load('proprietaire'),
            'userRole' => $user->role->value,
        ];

          switch ($user->role) {
            case UserRole::CLIENT:
                $selectedVehicule = Vehicule::find(session('selected_vehicule_id'));
                return array_merge($baseData, $this->getClientDashboardData($user, $selectedVehicule));

            case UserRole::VALIDATOR:
                return array_merge($baseData, $this->getValidatorDashboardData($user));

            case UserRole::ADMIN:
                return array_merge($baseData, $this->getAdminDashboardData($user));

            default:
                return $baseData;
        }
    }

    /**
     * Get client-specific dashboard data
     */

    private function getClientDashboardData($user, $selectedVehicule = null)
    {
        $vehicules = $user->vehicules()->with(['assurances', 'reparations', 'maintenances', 'ravitaillements'])->get();

        $dashboard = [
            'stats' => [
                'totalVehicules' => $vehicules->count(),
                'validatedVehicules' => $vehicules->where('status', VehiculeStatus::VALIDATED)->count(),
                'pendingVehicules' => $vehicules->where('status', VehiculeStatus::PENDING)->count(),
                'rejectedVehicules' => $vehicules->where('status', VehiculeStatus::REJECTED)->count(),
                'totalReparations' => $user->reparations()->count(),
                'totalMaintenances' => $user->maintenances()->count(),
                'monthlyFuelCost' => $user->ravitaillements()->whereMonth('ravitaillement_date', now()->month)->sum('total_cost'),
                'totalTrajets' => $user->trajets()->count(),
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

        if ($selectedVehicule) {
            $dashboard['selectedVehicule'] = $selectedVehicule;
        }

        return $dashboard ,;
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
