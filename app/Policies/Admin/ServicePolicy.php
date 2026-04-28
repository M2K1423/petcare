<?php

namespace App\Policies\Admin;

use App\Models\User;
use App\Models\Service;

class ServicePolicy extends AdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasRole('admin');
    }
}
