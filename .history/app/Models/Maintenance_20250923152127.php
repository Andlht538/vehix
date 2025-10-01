<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenanceFactory> */
    use HasFactory;

     protected $fillable = [
        'vehicle_id',
        'user_id',
        'maintenance_date',
        'type',
        'description',
        'cost',
        'mileage',
        'next_maintenance_date',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
        'mileage' => 'integer',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recus()
    {
        return $this->morphMany(Recu::class, 'receiptable');
    }

    public function isDue(): bool
    {
        return $this->next_maintenance_date && $this->next_maintenance_date->lte(now());
    }
}
