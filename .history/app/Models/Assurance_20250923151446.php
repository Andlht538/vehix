<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    /** @use HasFactory<\Database\Factories\AssuranceFactory> */
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'company',
        'policy_number',
        'start_date',
        'end_date',
        'premium',
        'deductible',
        'coverage_details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium' => 'decimal:2',
        'deductible' => 'decimal:2',
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

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date->lte(now()->addDays($days));
    }
}
