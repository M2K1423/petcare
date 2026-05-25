<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $session_id;
    public int $owner_id;
    public string $owner_name;
    public int $staff_id;
    public string $status;

    private int $targetUserId;

    public function __construct(ChatSession $session, int $targetUserId)
    {
        $this->session_id = $session->id;
        $this->owner_id = $session->owner_id;
        $this->owner_name = $session->owner->name ?? 'Unknown';
        $this->staff_id = $session->staff_id;
        $this->status = $session->status;
        $this->targetUserId = $targetUserId;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("chat.user.{$this->targetUserId}");
    }

    public function broadcastAs(): string
    {
        return 'chat.session.updated';
    }
}
