<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\AppNotification;

class NotificationService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): AppNotification
    {
        $notification = AppNotification::query()->create($attributes);

        broadcast(new NotificationCreated($notification));

        return $notification;
    }
}
