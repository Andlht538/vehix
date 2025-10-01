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
        'repair_date',
        'garage_name',
        'description',
        'cost',
        'mileage',
        'invoice_number',
    ];

    protected $casts = [
        'repair_date' => 'date',
        'cost' => 'decimal:2',
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
