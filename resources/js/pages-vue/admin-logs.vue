<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm người dùng..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      
      <select v-model="filterAction" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả hành động</option>
        <option value="create">Tạo</option>
        <option value="update">Sửa</option>
        <option value="delete">Xóa</option>
        <option value="login">Đăng Nhập</option>
        <option value="logout">Đăng Xuất</option>
      </select>

      <select v-model="filterEntity" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả Entity</option>
        <option value="User">Người Dùng</option>
        <option value="Doctor">Bác Sĩ</option>
        <option value="Service">Dịch Vụ</option>
        <option value="Medicine">Thuốc</option>
        <option value="Appointment">Lịch Hẹn</option>
      </select>

      <button @click="clearOldLogs" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
        🗑️ Xóa Cũ
      </button>
    </div>

    <!-- Activity Summary -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
        <div class="text-sm text-gray-600">Tổng Hoạt Động</div>
        <div class="text-3xl font-bold text-blue-600">{{ totalActivities }}</div>
      </div>
      <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
        <div class="text-sm text-gray-600">Hôm Nay</div>
        <div class="text-3xl font-bold text-green-600">{{ todayActivities }}</div>
      </div>
      <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
        <div class="text-sm text-gray-600">Tuần Này</div>
        <div class="text-3xl font-bold text-purple-600">{{ weekActivities }}</div>
      </div>
      <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-400">
        <div class="text-sm text-gray-600">Tháng Này</div>
        <div class="text-3xl font-bold text-orange-600">{{ monthActivities }}</div>
      </div>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Người Dùng</th>
            <th class="px-6 py-3 text-left">Hành Động</th>
            <th class="px-6 py-3 text-left">Entity</th>
            <th class="px-6 py-3 text-left">Chi Tiết</th>
            <th class="px-6 py-3 text-left">IP Address</th>
            <th class="px-6 py-3 text-left">Thời Gian</th>
            <th class="px-6 py-3 text-left">Hành Động</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ log.user?.name || 'Hệ Thống' }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                log.action === 'create' ? 'bg-green-100 text-green-800' :
                log.action === 'update' ? 'bg-blue-100 text-blue-800' :
                log.action === 'delete' ? 'bg-red-100 text-red-800' :
                log.action === 'login' ? 'bg-purple-100 text-purple-800' :
                'bg-gray-100 text-gray-800'
              ]">
                {{ actionLabel(log.action) }}
              </span>
            </td>
            <td class="px-6 py-4">{{ log.entity_type }}</td>
            <td class="px-6 py-4 text-xs text-gray-600 truncate max-w-xs" :title="log.description || 'N/A'">
              {{ log.description || 'N/A' }}
            </td>
            <td class="px-6 py-4 text-xs text-gray-500">{{ log.ip_address || 'N/A' }}</td>
            <td class="px-6 py-4 text-xs">{{ formatDate(log.created_at) }}</td>
            <td class="px-6 py-4">
              <button @click="viewDetails(log)" class="text-blue-600 hover:text-blue-800">👁️ Chi Tiết</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center gap-2">
      <button v-for="page in totalPages" :key="page" @click="currentPage = page"
        :class="['px-4 py-2 rounded', currentPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200']">
        {{ page }}
      </button>
    </div>

    <!-- Details Modal -->
    <div v-if="showDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg p-8 w-full max-w-2xl max-h-96 overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">📋 Chi Tiết Hoạt Động</h3>

        <div class="space-y-3 mb-6">
          <div>
            <strong class="text-gray-600">Người Dùng:</strong>
            <p>{{ selectedLog?.user?.name || 'Hệ Thống' }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Hành Động:</strong>
            <p>{{ actionLabel(selectedLog?.action) }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Entity:</strong>
            <p>{{ selectedLog?.entity_type }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Mô Tả:</strong>
            <p class="bg-gray-50 p-3 rounded">{{ selectedLog?.description }}</p>
          </div>
          <div v-if="selectedLog?.old_values">
            <strong class="text-gray-600">Giá Trị Cũ:</strong>
            <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto">{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
          </div>
          <div v-if="selectedLog?.new_values">
            <strong class="text-gray-600">Giá Trị Mới:</strong>
            <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto">{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
          </div>
          <div>
            <strong class="text-gray-600">IP Address:</strong>
            <p>{{ selectedLog?.ip_address }}</p>
          </div>
          <div>
            <strong class="text-gray-600">User Agent:</strong>
            <p class="text-xs">{{ selectedLog?.user_agent }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Thời Gian:</strong>
            <p>{{ formatDate(selectedLog?.created_at) }}</p>
          </div>
        </div>

        <button @click="showDetail = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400">
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const logs = ref([]);
const search = ref('');
const filterAction = ref('');
const filterEntity = ref('');
const currentPage = ref(1);
const showDetail = ref(false);
const selectedLog = ref(null);

const ITEMS_PER_PAGE = 20;

const formatDate = (date) => {
  return new Date(date).toLocaleString('vi-VN');
};

const actionLabel = (action) => {
  const labels = {
    'create': '✨ Tạo',
    'update': '✏️ Sửa',
    'delete': '🗑️ Xóa',
    'login': '🔓 Đăng Nhập',
    'logout': '🔒 Đăng Xuất'
  };
  return labels[action] || action;
};

const filteredLogs = computed(() => {
  let result = logs.value;
  
  if (search.value) {
    const q = search.value.toLowerCase();
    result = result.filter(log =>
      log.user?.name?.toLowerCase().includes(q) ||
      log.description?.toLowerCase().includes(q)
    );
  }

  if (filterAction.value) {
    result = result.filter(log => log.action === filterAction.value);
  }

  if (filterEntity.value) {
    result = result.filter(log => log.entity_type === filterEntity.value);
  }

  return result.slice((currentPage.value - 1) * ITEMS_PER_PAGE, currentPage.value * ITEMS_PER_PAGE);
});

const totalPages = computed(() => Math.ceil(logs.value.length / ITEMS_PER_PAGE));

const totalActivities = computed(() => logs.value.length);

const todayActivities = computed(() => {
  const today = new Date().toDateString();
  return logs.value.filter(log => new Date(log.created_at).toDateString() === today).length;
});

const weekActivities = computed(() => {
  const now = new Date();
  const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
  return logs.value.filter(log => new Date(log.created_at) > weekAgo).length;
});

const monthActivities = computed(() => {
  const now = new Date();
  const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
  return logs.value.filter(log => new Date(log.created_at) > monthAgo).length;
});

const fetchLogs = async () => {
  try {
    const res = await fetch('/api/admin/logs', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    logs.value = data.data;
  } catch (err) {
    console.error('Lỗi tải nhật ký:', err);
  }
};

const viewDetails = (log) => {
  selectedLog.value = log;
  showDetail.value = true;
};

const clearOldLogs = async () => {
  const days = prompt('Xóa nhật ký cũ hơn (ngày):', '30');
  if (days) {
    try {
      await fetch(`/api/admin/logs/clear-old?days=${days}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      fetchLogs();
      alert('✓ Xóa nhật ký cũ thành công!');
    } catch (err) {
      console.error('Lỗi xóa nhật ký:', err);
    }
  }
};

onMounted(fetchLogs);
</script>
