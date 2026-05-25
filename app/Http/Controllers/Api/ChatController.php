<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Events\ChatSessionEvent;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Role;
use App\Models\User;
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

        // Chỉ Owner mới cần lấy danh sách staff
        if (!$user->hasRole(Role::OWNER)) {
            return response()->json(['data' => []], 200);
        }

        $staff = User::whereHas('role', function ($q) {
            $q->whereIn('slug', [Role::VET, Role::RECEPTIONIST]);
        })
        ->where('is_locked', false)
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
                'latestMessage:id,chat_session_id,sender_id,body,created_at',
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

        if (!$user->hasRole(Role::OWNER)) {
            return response()->json(['message' => 'Chỉ chủ nuôi mới có thể khởi tạo phiên chat.'], 403);
        }

        $validated = $request->validate([
            'staff_id' => 'required|integer|exists:users,id',
        ]);

        // Kiểm tra staff phải là Vet hoặc Receptionist
        $staff = User::with('role')->findOrFail($validated['staff_id']);
        if (!in_array($staff->role->slug, [Role::VET, Role::RECEPTIONIST])) {
            return response()->json(['message' => 'Bạn chỉ có thể chat với Bác sĩ hoặc Lễ tân.'], 422);
        }

        // Nếu đã có phiên active → trả về phiên cũ
        $existing = ChatSession::active()
            ->where('owner_id', $user->id)
            ->where('staff_id', $staff->id)
            ->first();

        if ($existing) {
            $existing->load(['owner:id,name', 'staff:id,name', 'staff.role:id,name,slug']);
            return response()->json(['data' => $existing], 200);
        }

        $session = ChatSession::create([
            'owner_id' => $user->id,
            'staff_id' => $staff->id,
            'status' => 'active',
        ]);

        $session->load(['owner:id,name', 'staff:id,name', 'staff.role:id,name,slug']);

        // Thông báo cho staff có phiên chat mới
        broadcast(new ChatSessionEvent($session, $staff->id));

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
