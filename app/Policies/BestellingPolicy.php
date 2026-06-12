<?php

namespace App\Policies;

use App\Models\Bestelling;
use App\Models\Role;
use App\Models\User;

class BestellingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role_id === Role::TECHNIEKER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bestelling $bestelling): bool
    {
        return $user->role_id === Role::TECHNIEKER;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role_id === Role::TECHNIEKER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bestelling $bestelling): bool
    {
        return $user->role_id === Role::TECHNIEKER
            && $bestelling->gebruiker_id === $user->id
            && $bestelling->canStillBeEdited();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bestelling $bestelling): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bestelling $bestelling): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bestelling $bestelling): bool
    {
        return false;
    }
}
