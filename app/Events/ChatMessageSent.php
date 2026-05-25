<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public int $chat_session_id;
    public int $sender_id;
    public string $sender_name;
    public string $body;
    public string $created_at;

    public function __construct(ChatMessage $message)
    {
        $this->id = $message->id;
        $this->chat_session_id = $message->chat_session_id;
        $this->sender_id = $message->sender_id;
        $this->sender_name = $message->sender->name ?? 'Unknown';
        $this->body = $message->body;
        $this->created_at = $message->created_at->toIso8601String();
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("chat.session.{$this->chat_session_id}");
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }
}
