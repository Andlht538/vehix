<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VehiculeStatus;

class Vehicule extends Model
{
    /** @use HasFactory<\Database\Factories\VehiculeFactory> */
    use HasFactory;

     protected $fillable = [
        'user_id',
        'make',
        'model',
        'vehicule_type',
        'year',
        'license_plate',
        'vin',
        'color',
        'fuel_type',
        'mileage',
        'status',
        'validation_notes',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'status' => VehiculeStatus::class,
        'validated_at' => 'datetime',
        'year' => 'integer',
        'mileage' => 'integer',
    ];

        // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function reparations()
    {
        return $this->hasMany(Reparation::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function ravitaillements()
    {
        return $this->hasMany(Ravitaillement::class);
    }

    public function trajets()
    {
        return $this->hasMany(Trajet::class);
    }

    // Méthodes utilitaires
    public function isPending(): bool
    {
        return $this->status === VehiculeStatus::PENDING;
    }

    public function isValidated(): bool
    {
        return $this->status === VehiculeStatus::VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === VehiculeStatus::REJECTED;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    // Accesseurs pour les nouveaux champs
    public function getColorAttribute($value): ?string
    {
        return $value;
    }

    public function getFuelTypeAttribute($value): ?string
    {
        return $value;
    }

    public function getMileageAttribute($value): int
    {
        return (int) $value;
    }

    public function getVehicleTypeAttribute($value): ?string
    {
        return $value;
    }

    // Méthodes utilitaires pour le type de véhicule
    public function isCar(): bool
    {
        return $this->vehicle_type === 'voiture';
    }

    public function isMotorcycle(): bool
    {
        return $this->vehicle_type === 'moto';
    }

    public function isTruck(): bool
    {
        return $this->vehicle_type === 'camion';
    }

    public function isBus(): bool
    {
        return $this->vehicle_type === 'bus';
    }

    public function isVan(): bool
    {
        return $this->vehicle_type === 'utilitaire';
    }

    public function isScooter(): bool
    {
        return $this->vehicle_type === 'scooter';
    }

    public function isLightTruck(): bool
    {
        return $this->vehicle_type === 'camionnette';
    }

    // Méthodes utilitaires pour le type de carburant
    public function isGasoline(): bool
    {
        return $this->fuel_type === 'essence';
    }

    public function isDiesel(): bool
    {
        return $this->fuel_type === 'diesel';
    }

    public function isHybrid(): bool
    {
        return $this->fuel_type === 'hybride';
    }

    public function isElectric(): bool
    {
        return $this->fuel_type === 'electrique';
    }

    public function isGpl(): bool
    {
        return $this->fuel_type === 'gpl';
    }
}
