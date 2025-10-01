<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Vehicle;
use App\Models\Insurance;
use App\Models\Repair;
use App\Models\Maintenance;
use App\Models\Fueling;
use App\Models\Trip;
use App\Models\Receipt;
use App\Models\Owner;

use App\Policies\VehiclePolicy;
use App\Policies\InsurancePolicy;
use App\Policies\RepairPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\FuelingPolicy;
use App\Policies\TripPolicy;
use App\Policies\ReceiptPolicy;
use App\Policies\OwnerPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les mappings modèle → policy
     */
    protected $policies = [
        Vehicule::class     => VehiclePolicy::class,
        Insurance::class   => InsurancePolicy::class,
        Repair::class      => RepairPolicy::class,
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
