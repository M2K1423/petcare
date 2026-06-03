<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Events\ChatSessionEvent;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Role;
use App\Models\User;
use App\Services\AiIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Danh sách Vet + Receptionist để Owner chọn chat.
     */
    public function getStaffList(Request $request): JsonResponse
    {
        $user = $request->user();

        // Nếu admin hoặc lễ tân → trả về danh sách chủ nuôi (Owners) và Trợ lý AI (AI Assistant)
        if ($user->hasRole(Role::ADMIN) || $user->hasRole(Role::RECEPTIONIST)) {
            $recipients = User::whereHas('role', function ($q) {
                $q->whereIn('slug', [Role::OWNER, Role::AI_ASSISTANT]);
            })
            ->where(function ($query) {
                $query->where('is_locked', false)
                      ->orWhereHas('role', function ($q) {
                          $q->where('slug', Role::AI_ASSISTANT);
                      });
            })
            ->select('id', 'name', 'role_id')
            ->with('role:id,name,slug')
            ->get();

            return response()->json(['data' => $recipients], 200);
        }

        // Chỉ Owner mới cần lấy danh sách staff
        if (!$user->hasRole(Role::OWNER)) {
            return response()->json(['data' => []], 200);
        }

        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('slug', [Role::VET, Role::RECEPTIONIST, Role::AI_ASSISTANT, Role::ADMIN]);
        })
        ->where(function ($query) {
            $query->where('is_locked', false)
                  ->orWhereHas('role', function ($q) {
                      $q->where('slug', Role::AI_ASSISTANT);
                  });
        })
        ->select('id', 'name', 'role_id')
        ->with('role:id,name,slug')
        ->get();

        return response()->json(['data' => $staff], 200);
    }

    /**
     * Danh sách phiên chat active của user hiện tại.
     */
    public function mySessions(Request $request): JsonResponse
    {
        $user = $request->user();

        $sessions = ChatSession::active()
            ->forUser($user->id)
            ->with([
                'owner:id,name',
                'staff:id,name',
                'staff.role:id,name,slug',
                'latestMessage',
            ])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['data' => $sessions], 200);
    }

    /**
     * Owner tạo phiên chat mới với staff.
     */
    public function startSession(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole(Role::OWNER) && !$user->hasRole(Role::ADMIN) && !$user->hasRole(Role::RECEPTIONIST)) {
            return response()->json(['message' => 'Bạn không có quyền khởi tạo phiên chat.'], 403);
        }

        $validated = $request->validate([
            'staff_id' => 'nullable|integer|exists:users,id',
            'owner_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($user->hasRole(Role::OWNER)) {
            $staffId = $validated['staff_id'] ?? null;
            if (!$staffId) {
                return response()->json(['message' => 'staff_id là bắt buộc đối với chủ nuôi.'], 422);
            }
            $staff = User::with('role')->findOrFail($staffId);
            if (!in_array($staff->role->slug, [Role::VET, Role::RECEPTIONIST, Role::AI_ASSISTANT, Role::ADMIN])) {
                return response()->json(['message' => 'Bạn chỉ có thể chat với Bác sĩ, Lễ tân, Quản trị viên hoặc Trợ lý AI.'], 422);
            }
            $ownerId = $user->id;
        } else {
            // Admin hoặc Lễ tân khởi tạo phiên chat
            if (!empty($validated['staff_id'])) {
                $staffId = $validated['staff_id'];
                $staff = User::with('role')->findOrFail($staffId);
                if ($staff->role->slug !== Role::AI_ASSISTANT) {
                    return response()->json(['message' => 'Bạn chỉ có thể khởi tạo chat với Trợ lý AI hoặc Chủ nuôi.'], 422);
                }
                $ownerId = $user->id;
            } else {
                $ownerId = $validated['owner_id'] ?? null;
                if (!$ownerId) {
                    return response()->json(['message' => 'owner_id hoặc staff_id là bắt buộc.'], 422);
                }
                $owner = User::with('role')->findOrFail($ownerId);
                if ($owner->role->slug !== Role::OWNER) {
                    return response()->json(['message' => 'Bạn chỉ có thể khởi tạo chat với Chủ nuôi.'], 422);
                }
                $staffId = $user->id;
            }
        }

        // Nếu đã có phiên active → trả về phiên cũ
        $existing = ChatSession::active()
            ->where('owner_id', $ownerId)
            ->where('staff_id', $staffId)
            ->first();

        if ($existing) {
            $existing->load(['owner:id,name', 'staff:id,name', 'staff.role:id,name,slug']);
            return response()->json(['data' => $existing], 200);
        }

        $session = ChatSession::create([
            'owner_id' => $ownerId,
            'staff_id' => $staffId,
            'status' => 'active',
        ]);

        $session->load(['owner:id,name', 'staff:id,name', 'staff.role:id,name,slug']);

        // Thông báo cho người nhận tin
        $targetId = $user->id === $ownerId ? $staffId : $ownerId;
        broadcast(new ChatSessionEvent($session, $targetId));

        return response()->json(['data' => $session], 201);
    }

    /**
     * Lấy tin nhắn của phiên chat.
     */
    public function getMessages(Request $request, ChatSession $chatSession): JsonResponse
    {
        $user = $request->user();

        if (!$chatSession->isParticipant($user->id)) {
            return response()->json(['message' => 'Bạn không thuộc phiên chat này.'], 403);
        }

        $messages = $chatSession->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['data' => $messages], 200);
    }

    /**
     * Gửi tin nhắn trong phiên chat.
     */
    public function sendMessage(Request $request, ChatSession $chatSession): JsonResponse
    {
        $user = $request->user();

        if (!$chatSession->isParticipant($user->id)) {
            return response()->json(['message' => 'Bạn không thuộc phiên chat này.'], 403);
        }

        if ($chatSession->status !== 'active') {
            return response()->json(['message' => 'Phiên chat đã kết thúc.'], 422);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $message->load('sender:id,name');

        // Cập nhật updated_at của session để sort
        $chatSession->touch();

        // Broadcast tin nhắn real-time
        broadcast(new ChatMessageSent($message));

        // Kiểm tra nếu người nhận là AI Assistant
        $staff = User::with('role')->find($chatSession->staff_id);
        if ($staff && $staff->role->slug === Role::AI_ASSISTANT && $user->id !== $staff->id) {
            // Queue the command safely instead of popen/exec to avoid shell injection and command execution vulnerabilities
            \Illuminate\Support\Facades\Artisan::queue('ai:ask', [
                'session' => $chatSession->id,
                'message' => $message->body,
                'aiUserId' => $staff->id,
            ]);
        }




        return response()->json(['data' => $message], 201);
    }

    /**
     * Owner đóng phiên chat → xóa tin nhắn.
     */
    public function closeSession(Request $request, ChatSession $chatSession): JsonResponse
    {
        $user = $request->user();

        if (!$chatSession->isParticipant($user->id)) {
            return response()->json(['message' => 'Bạn không thuộc phiên chat này.'], 403);
        }

        // Thông báo cho đối phương
        $targetUserId = $chatSession->owner_id === $user->id
            ? $chatSession->staff_id
            : $chatSession->owner_id;

        $chatSession->update(['status' => 'closed']);

        broadcast(new ChatSessionEvent($chatSession, $targetUserId));

        // Xóa tin nhắn (session-based, không lưu lịch sử)
        $chatSession->messages()->delete();

        return response()->json(['message' => 'Đã kết thúc phiên chat.'], 200);
    }
}
