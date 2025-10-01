<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    /** @use HasFactory<\Database\Factories\TrajetFactory> */
    use HasFactory;

        protected $fillable = [
        'vehicle_id',
        'user_id',
        'trip_date',
        'departure',
        'destination',
        'distance',
        'purpose',
        'notes',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'distance' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
