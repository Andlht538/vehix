<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'login_attempts',
        'blocked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_until' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    // Relations
    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function repairs()
    {
        return $this->hasMany(Reparation::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function fuelings()
    {
        return $this->hasMany(Ravitaillement::class);
    }

    public function trips()
    {
        return $this->hasMany(Trajet::class);
    }

    public function receipts()
    {
        return $this->hasMany(Recu::class);
    }

    public function owner()
    {
        return $this->hasOne(Proprietaire::class);
    }

    // Méthodes utilitaires
    public function isClient(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    public function isValidator(): bool
    {
        return $this->role === UserRole::VALIDATOR;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function canValidateVehicles(): bool
    {
        return in_array($this->role, [UserRole::VALIDATOR, UserRole::ADMIN]);
    }

    public function isBlocked(): bool
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

}
