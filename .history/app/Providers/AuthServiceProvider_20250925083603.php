<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// Modèles
use App\Models\Vehicule;
use App\Models\Assurance;
use App\Models\Reparation;
use App\Models\Maintenance;
use App\Models\Ravitaillement;
use App\Models\Trajet;
use App\Models\Recu;
use App\Models\Proprietaire;
use App\Models\Ravitaillement;
// Policies
use App\Policies\VehiculePolicy;
use App\Policies\AssurancePolicy;
use App\Policies\ReparationPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\RavitaillementPolicy;
use App\Policies\TrajetPolicy;
use App\Policies\RecuPolicy;
use App\Policies\ProprietairePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les mappings modèle → policy
     */
    protected $policies = [
       Vehicule::class      => VehiculePolicy::class,
        Assurance::class    => AssurancePolicy::class,
        Reparation::class   => ReparationPolicy::class,
        Maintenance::class  => MaintenancePolicy::class,
        Ravitaillement::class    => RavitaillementPolicy::class,
        Trajet::class       => TrajetPolicy::class,
        Recu::class         => RecuPolicy::class,
        Proprietaire::class => ProprietairePolicy::class,

    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
