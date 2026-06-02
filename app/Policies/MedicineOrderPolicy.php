<?php

namespace App\Policies;

use App\Models\MedicineOrder;
use App\Models\User;

class MedicineOrderPolicy
{
    public function view(User $user, MedicineOrder $order): bool
    {
        return (int) $user->id === (int) $order->owner_id;
    }
}
