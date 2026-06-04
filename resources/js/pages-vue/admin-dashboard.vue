<template>
  <template v-if="!isLoading">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <!-- Summary Cards -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm">Tổng Người Dùng</div>
      <div class="text-3xl font-bold text-blue-600">{{ summary.total_users }}</div>
      <div class="text-xs text-gray-400 mt-2">{{ summary.active_users }} hoạt động</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm">Tổng Bác Sĩ</div>
      <div class="text-3xl font-bold text-green-600">{{ summary.total_doctors }}</div>
      <div class="text-xs text-gray-400 mt-2">{{ summary.total_pets }} thú cưng</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm">Lịch Hẹn Hôm Nay</div>
      <div class="text-3xl font-bold text-orange-600">{{ todayStats.appointments_today }}</div>
      <div class="text-xs text-gray-400 mt-2">{{ todayStats.appointments_completed_today }} hoàn thành</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm">Doanh Thu Hôm Nay</div>
      <div class="text-3xl font-bold text-purple-600">{{ formatCurrency(revenue.today) }}</div>
      <div class="text-xs text-gray-400 mt-2">Tháng: {{ formatCurrency(revenue.this_month) }}</div>
    </div>
  </div>

  <!-- Alerts -->
  <div class="space-y-3 mb-8" v-if="alerts.length > 0">
    <div v-for="alert in alerts" :key="alert.title" :class="[
      'p-4 rounded-lg text-white',
      alert.type === 'warning' ? 'bg-yellow-500' :
      alert.type === 'danger' ? 'bg-red-500' : 'bg-blue-500'
    ]">
      <div class="font-semibold">{{ alert.title }}</div>
      <div class="text-sm">{{ alert.message }}</div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-lg flex items-center gap-2">
            <ClipboardList class="w-5 h-5 text-slate-500" />
            <span>Hoạt Động Gần Đây</span>
        </h3>
      </div>
      <div class="divide-y">
        <div v-for="activity in recentActivity" :key="activity.id" class="px-6 py-3 text-sm hover:bg-gray-50">
          <div class="font-medium text-gray-800">{{ activity.user }}</div>
          <div class="text-gray-600">{{ activity.description }}</div>
          <div class="text-xs text-gray-400">{{ formatDate(activity.created_at) }}</div>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-lg flex items-center gap-2">
            <LayoutDashboard class="w-5 h-5 text-slate-500" />
            <span>Thống Kê</span>
        </h3>
      </div>
      <div class="space-y-3 p-6">
        <div class="flex justify-between items-center">
          <span>Người dùng bị khóa:</span>
          <span class="font-bold text-red-600">{{ summary.locked_users }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span>Thuốc sắp hết hạn:</span>
          <span class="font-bold flex items-center">
            <AlertTriangle v-if="alerts.filter(a => a.title === 'Expiring Medicines').length > 0" class="w-4 h-4 text-orange-600 inline" />
            <Check v-else class="w-4 h-4 text-green-600 inline" />
          </span>
        </div>
        <div class="flex justify-between items-center">
          <span>Thanh toán chờ xác nhận:</span>
          <span class="font-bold flex items-center">
            <Clock v-if="alerts.filter(a => a.title === 'Pending Payments').length > 0" class="w-4 h-4 text-yellow-600 inline animate-pulse" />
            <Check v-else class="w-4 h-4 text-green-600 inline" />
          </span>
        </div>
        <button @click="refreshDashboard" class="w-full mt-4 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 flex items-center justify-center gap-2 transition">
          <RefreshCw class="w-4 h-4" />
          <span>Làm Mới</span>
        </button>
      </div>
    </div>
  </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ClipboardList, LayoutDashboard, RefreshCw, AlertTriangle, Clock, Check } from '@lucide/vue';
import { useNotification } from '../composables/useNotification';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const { notifySuccess, handleApiError } = useNotification();

const summary = ref({});
const todayStats = ref({});
const revenue = ref({});
const alerts = ref([]);
const recentActivity = ref([]);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const formatDate = (date) => {
  return new Date(date).toLocaleString('vi-VN');
};

const isLoading = ref(true);

const fetchDashboard = async (showToast = false) => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/admin/dashboard', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    summary.value = data.summary || {};
    todayStats.value = data.today_stats || {};
    revenue.value = data.revenue || {};
    alerts.value = data.alerts || [];
    recentActivity.value = data.recent_activity || [];
    if (showToast) notifySuccess('Làm mới dữ liệu thành công!');
  } catch (err) {
    handleApiError(err);
  } finally {
    isLoading.value = false;
  }
};

const refreshDashboard = () => {
  fetchDashboard(true);
};

onMounted(() => {
  fetchDashboard();
});
</script>
