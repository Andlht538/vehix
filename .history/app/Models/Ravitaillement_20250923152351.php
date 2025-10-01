<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ravitaillement extends Model
{
    /** @use HasFactory<\Database\Factories\RavitaillementFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'user_id',
        'ravitaillement_date',
        'station_name',
        'liters',
        'price_per_liter',
        'total_cost',
        'mileage',
    ];

    protected $casts = [
        'fueling_date' => 'datetime',
        'liters' => 'decimal:2',
        'price_per_liter' => 'decimal:3',
        'total_cost' => 'decimal:2',
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
        return $this->morphMany(Receipt::class, 'receiptable');
    }
}
