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
        'year',
        'license_plate',
        'vin',
        'status',
        'validation_notes',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'status' => VehiculeStatus::class,
        'validated_at' => 'datetime',
        'year' => 'integer',
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
}
