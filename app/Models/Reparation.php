<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparation extends Model
{
    /** @use HasFactory<\Database\Factories\ReparationFactory> */
    use HasFactory;

     protected $fillable = [
        'vehicule_id',
        'user_id',
        'reparation_date',
        'garage_name',
        'description',
        'cost',
        'mileage',
        'invoice_number',
    ];

    protected $casts = [
        'reparation_date' => 'datetime',
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
        return $this->morphMany(Recu::class, 'recuable');
    }
}
