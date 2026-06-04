<template>
  <div :class="['fixed bottom-6 right-6 z-[100] flex flex-col items-end pointer-events-none', ['admin', 'receptionist'].includes(currentUser?.role) ? 'hidden md:flex' : 'flex']">
    
    <!-- Chat Window -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform translate-y-4 opacity-0 scale-95"
      enter-to-class="transform translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100 scale-100"
      leave-to-class="transform translate-y-4 opacity-0 scale-95"
    >
      <div v-if="isOpen" class="w-[350px] max-w-[calc(100vw-3rem)] h-[500px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-[0_12px_40px_-8px_rgba(0,0,0,0.2)] border border-[#E5E7EB] mb-4 flex flex-col overflow-hidden pointer-events-auto origin-bottom-right">
        
        <!-- Header -->
        <div class="bg-[#1D4ED8] p-4 text-white flex justify-between items-center shrink-0">
          <div class="flex items-center gap-3">
            <button v-if="currentSession && isStaff" @click="goBack" class="p-1.5 -ml-1.5 hover:bg-white/20 rounded-lg transition-colors">
              <ChevronLeft class="w-5 h-5" />
            </button>
            <div>
              <h3 class="font-semibold text-base leading-tight">
                {{ currentSession ? chatPartner?.name : 'Hỗ trợ trực tuyến' }}
              </h3>
              <p class="text-xs text-blue-100 mt-0.5">
                {{ currentSession ? 'Đang kết nối' : (isOwner ? 'Vui lòng chọn người hỗ trợ' : 'Danh sách tin nhắn') }}
              </p>
            </div>
          </div>
          <button @click="toggleOpen" class="p-1.5 -mr-1.5 hover:bg-white/20 rounded-lg transition-colors">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Body: Owner Selecting Staff -->
        <div v-if="isOwner && !currentSession" class="flex-1 overflow-y-auto p-4 bg-slate-50">
          <p class="text-sm text-slate-500 mb-3 font-medium">Chọn bác sĩ hoặc lễ tân để nhắn tin:</p>
          <div v-if="isLoadingStaff" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#1D4ED8]"></div>
          </div>
          <div v-else class="space-y-2">
            <button 
              v-for="staff in staffList" 
              :key="staff.id"
              @click="startSession(staff.id)"
              class="w-full bg-white border border-slate-200 rounded-xl p-3 flex items-center gap-3 hover:border-[#1D4ED8] hover:shadow-md transition-all text-left group"
            >
              <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                <Bot v-if="staff.role.slug === 'ai_assistant'" class="w-5 h-5" />
                <span v-else>{{ staff.name.charAt(0) }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-slate-900 truncate group-hover:text-[#1D4ED8] transition-colors">{{ staff.name }}</p>
                <p class="text-xs text-slate-500">{{ staff.role.name }}</p>
              </div>
              <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-[#1D4ED8]" />
            </button>
          </div>
        </div>

        <!-- Body: Staff Session List / Owner Selection -->
        <div v-if="isStaff && !currentSession" class="flex-1 overflow-y-auto bg-slate-50 flex flex-col">
          <!-- Admin/Receptionist header controls for selecting owners or AI -->
          <div v-if="['admin', 'receptionist'].includes(currentUser?.role) && !showOwnerSelection" class="p-3 bg-white border-b border-slate-100 flex justify-between items-center shrink-0 pointer-events-auto">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuộc trò chuyện</span>
            <button @click="openOwnerSelection" class="text-xs font-bold text-[#1D4ED8] hover:text-blue-700 flex items-center gap-1">
              <Plus class="w-3.5 h-3.5" />
              <span>Nhắn tin mới</span>
            </button>
          </div>

          <div v-if="['admin', 'receptionist'].includes(currentUser?.role) && showOwnerSelection" class="p-3 bg-white border-b border-slate-100 flex justify-between items-center shrink-0 pointer-events-auto">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Chọn người nhận</span>
            <button @click="showOwnerSelection = false" class="text-xs font-bold text-slate-500 hover:text-slate-700">
              <span>Quay lại</span>
            </button>
          </div>

          <!-- If showOwnerSelection is true (for admin or receptionist) -->
          <div v-if="['admin', 'receptionist'].includes(currentUser?.role) && showOwnerSelection" class="flex-1 overflow-y-auto p-4">
            <div v-if="isLoadingStaff" class="flex justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#1D4ED8]"></div>
            </div>
            <div v-else class="space-y-2 pointer-events-auto">
              <button 
                v-for="target in staffList" 
                :key="target.id"
                @click="startSession(target.id)"
                class="w-full bg-white border border-slate-200 rounded-xl p-3 flex items-center gap-3 hover:border-[#1D4ED8] hover:shadow-md transition-all text-left group"
              >
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                  <Bot v-if="target.role.slug === 'ai_assistant'" class="w-5 h-5" />
                  <span v-else>{{ target.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-slate-900 truncate group-hover:text-[#1D4ED8] transition-colors">{{ target.name }}</p>
                  <p class="text-xs text-slate-500">{{ target.role.name }}</p>
                </div>
                <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-[#1D4ED8]" />
              </button>
            </div>
          </div>

          <!-- Regular session list -->
          <template v-else>
            <div v-if="isLoadingSessions" class="flex justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#1D4ED8]"></div>
            </div>
            <div v-else-if="sessions.length === 0" class="flex flex-col items-center justify-center h-full text-center p-6 text-slate-400">
              <MessageSquare class="w-12 h-12 mb-3" />
              <p>Không có tin nhắn nào</p>
            </div>
            <div v-else class="divide-y divide-slate-100 overflow-y-auto pointer-events-auto">
              <button 
                v-for="session in sessions" 
                :key="session.id"
                @click="openSession(session)"
                class="w-full bg-white p-4 flex items-center gap-3 hover:bg-blue-50 transition-colors text-left group"
              >
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                  <Bot v-if="getSessionPartner(session)?.role?.slug === 'ai_assistant'" class="w-6 h-6" />
                  <span v-else>{{ getSessionPartner(session)?.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-baseline mb-0.5">
                    <p class="font-medium text-slate-900 truncate">{{ getSessionPartner(session)?.name }}</p>
                    <span class="text-[10px] text-slate-400 whitespace-nowrap ml-2" v-if="session.latest_message">
                      {{ formatTime(session.latest_message.created_at) }}
                    </span>
                  </div>
                  <p class="text-sm text-slate-500 truncate" :class="{'text-slate-900 font-medium': hasUnread(session)}">
                    {{ session.latest_message ? session.latest_message.body : 'Bắt đầu trò chuyện' }}
                  </p>
                </div>
                <div v-if="hasUnread(session)" class="w-2.5 h-2.5 bg-red-500 rounded-full shrink-0"></div>
              </button>
            </div>
          </template>
        </div>

        <!-- Body: Chat Interface -->
        <div v-if="currentSession" class="flex-1 flex flex-col bg-white overflow-hidden relative">
          <!-- Messages Area -->
          <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-if="isLoadingMessages" class="flex justify-center py-4">
              <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#1D4ED8]"></div>
            </div>
            
            <div v-for="(msg, index) in messages" :key="msg.id" class="flex flex-col" :class="msg.sender_id === currentUser.id ? 'items-end' : 'items-start'">
              <span v-if="showSenderName(index)" class="text-[10px] text-slate-400 mb-1 ml-1">{{ msg.sender.name }}</span>
              <div 
                class="max-w-[80%] px-3.5 py-2.5 rounded-2xl text-[14px] leading-relaxed break-words"
                :class="msg.sender_id === currentUser.id ? 'bg-[#1D4ED8] text-white rounded-br-sm' : 'bg-[#F1F5F9] text-slate-800 rounded-bl-sm'"
              >
                {{ msg.body }}
              </div>
            </div>
            
            <div v-if="messages.length === 0 && !isLoadingMessages" class="text-center text-sm text-slate-400 mt-10">
              Hãy gửi tin nhắn đầu tiên
            </div>
          </div>

          <!-- Input Area -->
          <div class="border-t border-slate-200 bg-white p-3 shrink-0">
            <div v-if="isOwner" class="mb-2 flex justify-end">
              <button @click="closeSession" class="text-[11px] text-red-500 hover:text-red-600 hover:underline px-1 py-0.5 rounded transition-colors">
                Kết thúc cuộc trò chuyện
              </button>
            </div>
            <form @submit.prevent="sendMessage" class="flex items-end gap-2">
              <div class="flex-1 relative">
                <textarea 
                  v-model="newMessage" 
                  @keydown.enter.prevent="sendMessage"
                  placeholder="Nhập tin nhắn..." 
                  class="w-full bg-[#F8FAFC] border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1D4ED8] resize-none overflow-hidden max-h-24 min-h-[44px]"
                  rows="1"
                  ref="messageInput"
                  @input="adjustTextareaHeight"
                ></textarea>
              </div>
              <button 
                type="submit" 
                :disabled="!newMessage.trim() || isSending"
                class="w-11 h-11 shrink-0 bg-[#1D4ED8] text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <SendHorizontal v-if="!isSending" class="w-5 h-5 translate-x-px -translate-y-px" />
                <div v-else class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
              </button>
            </form>
          </div>
        </div>

      </div>
    </transition>

    <!-- Toggle Button -->
    <button 
      @click="toggleOpen"
      class="w-14 h-14 bg-[#1D4ED8] text-white rounded-full shadow-[0_8px_20px_-4px_rgba(29,78,216,0.5)] flex items-center justify-center hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all pointer-events-auto relative z-10"
    >
      <MessageSquareMore v-if="!isOpen" class="w-7 h-7" />
      <X v-else class="w-6 h-6" />
      
      <!-- Unread Badge -->
      <span v-if="totalUnread > 0 && !isOpen" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white rounded-full text-[10px] font-bold flex items-center justify-center">
        {{ totalUnread }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { ChevronLeft, X, ChevronRight, MessageSquare, SendHorizontal, MessageSquareMore, Plus, Bot } from '@lucide/vue';
import { callApi } from '../auth/http';

const http = {
  get: (url) => callApi(url, 'GET'),
  post: (url, body) => callApi(url, 'POST', body),
  patch: (url, body) => callApi(url, 'PATCH', body),
};
import { useNotification } from '../composables/useNotification';
import { subscribeChatSession, unsubscribeChatSession } from '../realtime';

const { notifyError } = useNotification();

const isOpen = ref(false);
const currentUser = ref(null);
const isLoadingStaff = ref(false);
const staffList = ref([]);
const sessions = ref([]);
const isLoadingSessions = ref(false);
const showOwnerSelection = ref(false);

const currentSession = ref(null);
const messages = ref([]);
const isLoadingMessages = ref(false);
const newMessage = ref('');
const isSending = ref(false);

const messagesContainer = ref(null);
const messageInput = ref(null);

const unreadMap = ref(new Map());

// Computed
const isOwner = computed(() => currentUser.value?.role === 'owner');
const isStaff = computed(() => ['vet', 'receptionist', 'admin'].includes(currentUser.value?.role));
const totalUnread = computed(() => {
  let count = 0;
  for (let val of unreadMap.value.values()) {
    if (val) count++;
  }
  return count;
});

const chatPartner = computed(() => {
  if (!currentSession.value || !currentUser.value) return null;
  return currentSession.value.owner_id === currentUser.value.id
    ? currentSession.value.staff
    : currentSession.value.owner;
});

const getSessionPartner = (session) => {
  if (!session || !currentUser.value) return null;
  return session.owner_id === currentUser.value.id ? session.staff : session.owner;
};

// Methods
const toggleOpen = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    if (isOwner.value && !currentSession.value) {
      checkActiveSession();
      fetchStaffList();
    } else if (isStaff.value && !currentSession.value) {
      fetchSessions();
    } else if (currentSession.value) {
      scrollToBottom();
      markSessionAsRead(currentSession.value.id);
    }
  }
};

const goBack = () => {
  if (currentSession.value) {
    unsubscribeChatSession(currentSession.value.id);
  }
  currentSession.value = null;
  messages.value = [];
  fetchSessions();
};

const fetchCurrentUser = async () => {
  try {
    const response = await http.get('/api/user');
    currentUser.value = response;
  } catch (error) {
    console.error('Lỗi lấy user chat:', error);
  }
};

const fetchStaffList = async () => {
  isLoadingStaff.value = true;
  try {
    const res = await http.get('/api/chat/staff');
    staffList.value = res.data;
  } catch (err) {
    notifyError(err, 'Lỗi tải danh sách nhân viên');
  } finally {
    isLoadingStaff.value = false;
  }
};

const fetchSessions = async () => {
  isLoadingSessions.value = true;
  try {
    const res = await http.get('/api/chat/sessions');
    sessions.value = res.data;
  } catch (err) {
    notifyError(err, 'Lỗi tải danh sách cuộc trò chuyện');
  } finally {
    isLoadingSessions.value = false;
  }
};

const checkActiveSession = async () => {
  try {
    const res = await http.get('/api/chat/sessions');
    if (res.data && res.data.length > 0) {
      // Owner only has max 1 active session at a time in this simple version
      openSession(res.data[0]);
    }
  } catch (err) {
    console.error('Lỗi check session:', err);
  }
};

const startSession = async (targetId) => {
  try {
    const targetUser = staffList.value.find(u => u.id === targetId);
    const isTargetAi = targetUser?.role?.slug === 'ai_assistant';
    
    const payload = {};
    if (['admin', 'receptionist'].includes(currentUser.value?.role)) {
      if (isTargetAi) {
        payload.staff_id = targetId;
      } else {
        payload.owner_id = targetId;
      }
    } else if (isOwner.value) {
      payload.staff_id = targetId;
    }

    const res = await http.post('/api/chat/sessions', payload);
    if (['admin', 'receptionist'].includes(currentUser.value?.role)) {
      showOwnerSelection.value = false;
    }
    openSession(res.data);
  } catch (err) {
    notifyError(err, 'Lỗi khởi tạo cuộc trò chuyện');
  }
};

const openOwnerSelection = () => {
  showOwnerSelection.value = true;
  fetchStaffList();
};

const openSession = async (session) => {
  currentSession.value = session;
  markSessionAsRead(session.id);
  await fetchMessages(session.id);
  
  // Realtime subscription
  subscribeChatSession(session.id, (payload) => {
    // Check if message belongs to current session
    if (payload && payload.chat_session_id === currentSession.value?.id) {
      // Only add if not already present (avoid duplicates from own sent messages)
      if (!messages.value.some(m => m.id === payload.id)) {
        messages.value.push(payload);
        scrollToBottom();
      }
    }
  });
};

const fetchMessages = async (sessionId) => {
  isLoadingMessages.value = true;
  try {
    const res = await http.get(`/api/chat/sessions/${sessionId}/messages`);
    messages.value = res.data;
    scrollToBottom();
  } catch (err) {
    notifyError(err, 'Lỗi tải tin nhắn');
  } finally {
    isLoadingMessages.value = false;
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || isSending.value || !currentSession.value) return;
  
  const text = newMessage.value.trim();
  newMessage.value = '';
  adjustTextareaHeight();
  
  isSending.value = true;
  try {
    const res = await http.post(`/api/chat/sessions/${currentSession.value.id}/messages`, {
      body: text
    });
    
    // Only push if the websocket hasn't already added it
    if (!messages.value.some(m => m.id === res.data.id)) {
      messages.value.push(res.data);
      scrollToBottom();
    }
  } catch (err) {
    newMessage.value = text; // Restore if failed
    notifyError(err, 'Không thể gửi tin nhắn');
  } finally {
    isSending.value = false;
    nextTick(() => {
      messageInput.value?.focus();
    });
  }
};

const closeSession = async () => {
  if (!confirm('Bạn có chắc chắn muốn kết thúc cuộc trò chuyện này? Toàn bộ nội dung sẽ bị xóa.')) return;
  
  if (currentSession.value) {
    try {
      await http.patch(`/api/chat/sessions/${currentSession.value.id}/close`);
      unsubscribeChatSession(currentSession.value.id);
      currentSession.value = null;
      messages.value = [];
      fetchStaffList(); // Back to staff selection
    } catch (err) {
      notifyError(err, 'Lỗi kết thúc trò chuyện');
    }
  }
};

// Utilities
const adjustTextareaHeight = () => {
  if (!messageInput.value) return;
  const el = messageInput.value;
  el.style.height = 'auto';
  el.style.height = (el.scrollHeight) + 'px';
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  const d = new Date(isoString);
  return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
};

const showSenderName = (index) => {
  if (index === 0) return true;
  const curr = messages.value[index];
  const prev = messages.value[index - 1];
  return curr.sender_id !== prev.sender_id;
};

const markSessionAsRead = (sessionId) => {
  unreadMap.value.set(sessionId, false);
};

const hasUnread = (session) => {
  return unreadMap.value.get(session.id) === true;
};

// Event Listeners
const handleSessionUpdatedEvent = (e) => {
  const detail = e.detail;
  if (!detail) return;
  
  // Reload sessions list for staff
  if (isStaff.value) {
    fetchSessions();
    if (!isOpen.value || (currentSession.value && currentSession.value.id !== detail.session_id)) {
       unreadMap.value.set(detail.session_id, true);
    }
    
    // If current session was closed by owner
    if (currentSession.value && currentSession.value.id === detail.session_id && detail.status === 'closed') {
       alert('Chủ nuôi đã kết thúc cuộc trò chuyện này.');
       goBack();
    }
  }
};

onMounted(async () => {
  await fetchCurrentUser();
  
  if (currentUser.value && ['owner', 'vet', 'receptionist', 'admin'].includes(currentUser.value.role)) {
    window.addEventListener('petcare-chat-session-updated', handleSessionUpdatedEvent);
    
    // Fetch initial state for unread badge without opening
    if (isStaff.value) {
      fetchSessions();
    } else if (isOwner.value) {
      checkActiveSession(); // owner only has 1 max active session
    }
  }
});

onUnmounted(() => {
  window.removeEventListener('petcare-chat-session-updated', handleSessionUpdatedEvent);
  if (currentSession.value) {
    unsubscribeChatSession(currentSession.value.id);
  }
});

watch(isOpen, (newVal) => {
  if (newVal) {
    nextTick(() => {
      adjustTextareaHeight();
      scrollToBottom();
    });
  }
});

</script>

<style scoped>
/* Tùy chỉnh scrollbar cho chat */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #CBD5E1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94A3B8;
}
</style>
