<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\Role;
use App\Models\User;

class PetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::OWNER);
    }

    public function view(User $user, Pet $pet): bool
    {
        return $user->hasRole(Role::OWNER) && $pet->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::OWNER);
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->hasRole(Role::OWNER) && $pet->owner_id === $user->id;
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->hasRole(Role::OWNER) && $pet->owner_id === $user->id;
    }
}
