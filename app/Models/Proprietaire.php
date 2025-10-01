<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietaire extends Model
{
    /** @use HasFactory<\Database\Factories\ProprietaireFactory> */
    use HasFactory;

        protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'driver_license_number',
        'driver_license_expiry',
    ];

    protected $casts = [
        'driver_license_expiry' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isLicenseExpiring(int $days = 30): bool
    {
        return $this->driver_license_expiry->lte(now()->addDays($days));
    }
}
