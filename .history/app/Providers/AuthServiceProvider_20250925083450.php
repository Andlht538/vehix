<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// Modèles
use App\Models\Vehicule;
use App\Models\Assurance;
use App\Models\Reparation;
use App\Models\Maintenance;
use App\Models\Carburant;
use App\Models\Trajet;
use App\Models\Recu;
use App\Models\Proprietaire;

// Policies
use App\Policies\VehiculePolicy;
use App\Policies\AssurancePolicy;
use App\Policies\ReparationPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\CarburantPolicy;
use App\Policies\TrajetPolicy;
use App\Policies\RecuPolicy;
use App\Policies\ProprietairePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les mappings modèle → policy
     */
    protected $policies = [
        Vehicule::class     => VehiculePolicy::class,
        Assurance::class   => AssurancePolicy::class,
        Reparation::class      => RepairPolicy::class,
        Maintenance::class => MaintenancePolicy::class,
        Fueling::class     => FuelingPolicy::class,
        Trip::class        => TripPolicy::class,
        Receipt::class     => ReceiptPolicy::class,
        Owner::class       => OwnerPolicy::class,

    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
