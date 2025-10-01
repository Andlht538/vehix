<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status' => VehicleStatus::class,
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

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function fuelings()
    {
        return $this->hasMany(Fueling::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
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
        return $this->status === VehicleStatus::REJECTED;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }
}
