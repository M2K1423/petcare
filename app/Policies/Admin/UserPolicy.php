<?php

namespace App\Policies\Admin;

use App\Models\User;

class UserPolicy extends AdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $model->id !== $user->id;
    }

    public function lock(User $user, User $model): bool
    {
        return $user->hasRole('admin') && $model->id !== $user->id;
    }

    public function unlock(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    public function resetPassword(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }

    public function assignRole(User $user, User $model): bool
    {
        return $user->hasRole('admin');
    }
}
