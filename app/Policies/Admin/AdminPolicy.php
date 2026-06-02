<?php

namespace App\Policies\Admin;

use App\Models\User;

class AdminPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }
}
