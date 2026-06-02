<?php

namespace App\Policies\Admin;

use App\Models\User;
use App\Models\Doctor;

class DoctorPolicy extends AdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->hasRole('admin');
    }
}
