<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Events\ChatMessageSent;

class AiIntegrationService
{
    /**
     * Gửi tin nhắn tới AI-service và trả về kết quả
     */
    public function askAi(ChatSession $session, string $message, int $aiUserId): bool
    {
        $owner = $session->owner;
        
        $payload = [
            'user_id' => 'owner_' . $owner->id,
            'message' => $message,
            'context' => [
                'customer_name' => $owner->name,
                'customer_phone' => $owner->phone,
            ]
        ];

        $aiUrl = env('AI_SERVICE_URL', 'http://localhost:8001/api/v1/chat');
        $apiKey = env('AI_SERVICE_API_KEY', 'petcare-ai-key');

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($aiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['message'] ?? 'Xin lỗi, tôi không thể trả lời lúc này.';

                // Tạo tin nhắn phản hồi từ AI
                $aiMessage = ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_id' => $aiUserId,
                    'body' => $reply,
                ]);

                $aiMessage->load('sender:id,name');
                $session->touch();

                // Broadcast phản hồi
                broadcast(new ChatMessageSent($aiMessage));

                return true;
            } else {
                Log::error('AI-service trả về lỗi: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối AI-service: ' . $e->getMessage());
        }

        return false;
    }
}
