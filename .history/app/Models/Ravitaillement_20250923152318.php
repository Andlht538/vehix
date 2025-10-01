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
        'fueling_date',
        'station_name',
        'liters',
        'price_per_liter',
        'total_cost',
        'mileage',
    ];

    protected $casts = [
        'fueling_date' => 'date',
        'liters' => 'decimal:2',
        'price_per_liter' => 'decimal:3',
        'total_cost' => 'decimal:2',
        'mileage' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receipts()
    {
        return $this->morphMany(Receipt::class, 'receiptable');
    }
}
