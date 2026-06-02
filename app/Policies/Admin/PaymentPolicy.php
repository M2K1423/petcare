<?php

namespace App\Policies\Admin;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy extends AdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    public function confirm(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin');
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') && $payment->status === 'completed';
    }
}
