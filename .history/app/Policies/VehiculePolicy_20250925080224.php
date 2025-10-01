<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicule;
use App\Enums\UserRole;
use Illuminate\Auth\Access\Response;

class VehiculePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir la liste
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vehicule $vehicule): bool
    {
         return $user->isAdmin() ||
               $user->isValidator() ||
               $vehicule->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isClient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicule $vehicule): bool
    {
        return $user->isAdmin() ||
               ($user->isClient() && $vehicule->user_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicule $vehicule): bool
    {
         return $user->isAdmin() || 
               ($user->isClient() && $vehicule->user_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vehicule $vehicule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vehicule $vehicule): bool
    {
        return false;
    }
}
