<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDiagnosisController extends Controller
{
    /**
     * Gửi yêu cầu chẩn đoán triệu chứng đến AI-service và trả về kết quả.
     */
    public function diagnose(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role->slug, ['owner', 'vet'])) {
            return response()->json(['message' => 'Bạn không có quyền truy cập tính năng này.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'pet_type' => 'nullable|string|max:50',
            'pet_age' => 'nullable|string|max:50',
            'vaccination' => 'nullable|string|max:50',
        ]);

        $payload = [
            'user_id' => 'user_' . $user->id,
            'message' => $validated['message'],
            'context' => [
                'customer_name' => $user->name,
                'role' => $user->role->slug,
                'pet_type' => $validated['pet_type'] ?? 'Chưa xác định',
                'pet_age' => $validated['pet_age'] ?? 'Chưa xác định',
                'vaccination' => $validated['vaccination'] ?? 'Chưa xác định',
            ]
        ];

        $aiUrl = config('services.ai.url');
        $apiKey = config('services.ai.key');

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($aiUrl, $payload);

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }

            Log::error('AI-service chẩn đoán trả về lỗi: ' . $response->body());
            return response()->json([
                'message' => 'AI Service trả về lỗi hoặc không hoạt động. Vui lòng thử lại sau.'
            ], 502);

        } catch (\Exception $e) {
            Log::error('Lỗi kết nối AI-service khi chẩn đoán: ' . $e->getMessage());
            return response()->json([
                'message' => 'Không thể kết nối đến máy chủ AI. Vui lòng kiểm tra lại dịch vụ.'
            ], 500);
        }
    }
}
