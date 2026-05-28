<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatSession;
use App\Services\AiIntegrationService;
use Illuminate\Support\Facades\Log;

class AiAskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:ask {session} {message} {aiUserId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi tin nhắn sang AI-service bất đồng bộ dưới nền';

    /**
     * Execute the console command.
     */
    public function handle(AiIntegrationService $aiIntegrationService)
    {
        $sessionId = $this->argument('session');
        $message = $this->argument('message');
        $aiUserId = $this->argument('aiUserId');

        Log::info("🤖 Artisan Command ai:ask bắt đầu cho session {$sessionId}");

        $session = ChatSession::find($sessionId);
        if (!$session) {
            Log::error("Artisan Command ai:ask lỗi: Không tìm thấy session {$sessionId}");
            return Command::FAILURE;
        }

        $result = $aiIntegrationService->askAi($session, $message, $aiUserId);

        if ($result) {
            Log::info("Artisan Command ai:ask hoàn thành thành công");
            return Command::SUCCESS;
        }

        Log::error("Artisan Command ai:ask thất bại");
        return Command::FAILURE;
    }
}
