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

    // Relationships
    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function proprietaire()
    {
        return $this->hasOne(Proprietaire::class);
    }

    public function reparations()
    {
        return $this->hasManyThrough(Reparation::class, Vehicule::class);
    }

    public function maintenances()
    {
        return $this->hasManyThrough(Maintenance::class, Vehicule::class);
    }

    public function ravitaillements()
    {
        return $this->hasManyThrough(Ravitaillement::class, Vehicule::class);
    }

    public function assurances()
    {
        return $this->hasManyThrough(Assurance::class, Vehicule::class);
    }

    public function trajets()
    {
        return $this->hasManyThrough(Trajet::class, Vehicule::class);
    }

    // Role checking methods
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
        return $this->return $this->role === \App\Enums\UserRole::ADMIN;
    }

    public function canValidateVehicules(): bool
    {
        return in_array($this->role, [UserRole::VALIDATOR, UserRole::ADMIN]);
    }

    // Vehicle management methods
    public function hasValidatedVehicule(): bool
    {
        return $this->vehicules()
            ->where('status', \App\Enums\VehiculeStatus::VALIDATED)
            ->exists();
    }

    public function hasPendingVehicule(): bool
    {
        return $this->vehicules()
            ->where('status', \App\Enums\VehiculeStatus::PENDING)
            ->exists();
    }

    public function hasAnyVehicule(): bool
    {
        return $this->vehicules()->exists();
    }

    public function getValidatedVehicules()
    {
        return $this->vehicules()
            ->where('status', \App\Enums\VehiculeStatus::VALIDATED)
            ->get();
    }

    public function getSelectedVehicule()
    {
        $vehiculeId = session('selected_vehicule_id');
        
        if (!$vehiculeId) {
            return null;
        }

        return $this->vehicules()
            ->where('id', $vehiculeId)
            ->where('status', \App\Enums\VehiculeStatus::VALIDATED)
            ->first();
    }

    // Account status methods
    public function isBlocked(): bool
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }

    public function canLogin(): bool
    {
        return !$this->isBlocked();
    }
    

}
