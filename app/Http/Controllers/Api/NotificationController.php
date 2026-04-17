<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = AppNotification::where('user_id', $request->user()->id)->findOrFail($id);
        
        if (!$notification->read_at) {
            $notification->update(['read_at' => Carbon::now()]);
        }

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }
}
