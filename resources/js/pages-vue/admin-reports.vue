<template>
  <div class="space-y-6">
    <!-- Report Type Selector -->
    <div class="flex gap-4">
      <button v-for="type in reportTypes" :key="type" @click="currentReport = type"
        :class="[
          'px-6 py-2 rounded-lg font-semibold',
          currentReport === type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'
        ]">
        {{ type }}
      </button>
    </div>

    <!-- Date Range Filter -->
    <div class="flex gap-4 bg-white p-4 rounded-lg shadow">
      <div>
        <label class="block text-sm font-medium mb-2">Từ Ngày</label>
        <input v-model="startDate" type="date" class="px-4 py-2 border rounded">
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Đến Ngày</label>
        <input v-model="endDate" type="date" class="px-4 py-2 border rounded">
      </div>
      <div class="flex items-end">
        <button @click="generateReport" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
          📊 Tạo Báo Cáo
        </button>
      </div>
    </div>

    <!-- Report Display -->
    <div v-if="currentReport === 'Lịch Hẹn'" class="space-y-4">
      <div class="grid grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
          <div class="text-sm text-gray-600">Tổng Lịch Hẹn</div>
          <div class="text-3xl font-bold text-blue-600">{{ appointmentStats.total }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
          <div class="text-sm text-gray-600">Hoàn Thành</div>
          <div class="text-3xl font-bold text-green-600">{{ appointmentStats.completed }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
          <div class="text-sm text-gray-600">Đã Lên Lịch</div>
          <div class="text-3xl font-bold text-yellow-600">{{ appointmentStats.scheduled }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
          <div class="text-sm text-gray-600">Đã Hủy</div>
          <div class="text-3xl font-bold text-red-600">{{ appointmentStats.cancelled }}</div>
        </div>
      </div>
    </div>

    <div v-if="currentReport === 'Doanh Thu'" class="space-y-4">
      <div class="grid grid-cols-4 gap-4">
        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400">
          <div class="text-sm text-gray-600">Tổng Doanh Thu</div>
          <div class="text-2xl font-bold text-purple-600">{{ formatCurrency(revenueStats.total) }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
          <div class="text-sm text-gray-600">Thanh Toán Thành Công</div>
          <div class="text-2xl font-bold text-green-600">{{ formatCurrency(revenueStats.completed) }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
          <div class="text-sm text-gray-600">Chờ Xác Nhận</div>
          <div class="text-2xl font-bold text-yellow-600">{{ formatCurrency(revenueStats.pending) }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
          <div class="text-sm text-gray-600">Hoàn Tiền</div>
          <div class="text-2xl font-bold text-red-600">{{ formatCurrency(revenueStats.refunded) }}</div>
        </div>
      </div>
    </div>

    <div v-if="currentReport === 'Hiệu Suất Bác Sĩ'" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Tên Bác Sĩ</th>
            <th class="px-6 py-3 text-right">Số Lịch Hẹn</th>
            <th class="px-6 py-3 text-right">Doanh Thu</th>
            <th class="px-6 py-3 text-right">Đánh Giá TB</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="doctor in doctorPerformance" :key="doctor.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ doctor.name }}</td>
            <td class="px-6 py-4 text-right">{{ doctor.appointments }}</td>
            <td class="px-6 py-4 text-right font-bold">{{ formatCurrency(doctor.revenue) }}</td>
            <td class="px-6 py-4 text-right">{{ doctor.rating }}/5 ⭐</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="currentReport === 'Dịch Vụ Phổ Biến'" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Tên Dịch Vụ</th>
            <th class="px-6 py-3 text-right">Số Lần Sử Dụng</th>
            <th class="px-6 py-3 text-right">Doanh Thu</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="service in servicePopularity" :key="service.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ service.name }}</td>
            <td class="px-6 py-4 text-right">{{ service.usage_count }}</td>
            <td class="px-6 py-4 text-right font-bold">{{ formatCurrency(service.revenue) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="currentReport === 'Khách Hàng'" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Tên Khách Hàng</th>
            <th class="px-6 py-3 text-right">Số Lần Ghé Thăm</th>
            <th class="px-6 py-3 text-right">Tổng Chi Tiêu</th>
            <th class="px-6 py-3 text-left">Thú Cưng</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="customer in customerStats" :key="customer.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ customer.name }}</td>
            <td class="px-6 py-4 text-right">{{ customer.visits }}</td>
            <td class="px-6 py-4 text-right font-bold">{{ formatCurrency(customer.spent) }}</td>
            <td class="px-6 py-4">{{ customer.pets }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const reportTypes = ['Lịch Hẹn', 'Doanh Thu', 'Hiệu Suất Bác Sĩ', 'Dịch Vụ Phổ Biến', 'Khách Hàng'];
const currentReport = ref('Lịch Hẹn');
const startDate = ref(new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]);
const endDate = ref(new Date().toISOString().split('T')[0]);

const appointmentStats = ref({ total: 0, completed: 0, scheduled: 0, cancelled: 0 });
const revenueStats = ref({ total: 0, completed: 0, pending: 0, refunded: 0 });
const doctorPerformance = ref([]);
const servicePopularity = ref([]);
const customerStats = ref([]);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const generateReport = async () => {
  try {
    let endpoint = '';
    switch (currentReport.value) {
      case 'Lịch Hẹn':
        endpoint = `/api/admin/reports/appointments?start_date=${startDate.value}&end_date=${endDate.value}`;
        break;
      case 'Doanh Thu':
        endpoint = `/api/admin/reports/revenue?start_date=${startDate.value}&end_date=${endDate.value}`;
        break;
      case 'Hiệu Suất Bác Sĩ':
        endpoint = `/api/admin/reports/doctor-performance?start_date=${startDate.value}&end_date=${endDate.value}`;
        break;
      case 'Dịch Vụ Phổ Biến':
        endpoint = `/api/admin/reports/service-popularity?start_date=${startDate.value}&end_date=${endDate.value}`;
        break;
      case 'Khách Hàng':
        endpoint = `/api/admin/reports/customers?start_date=${startDate.value}&end_date=${endDate.value}`;
        break;
    }

    const res = await fetch(endpoint, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();

    switch (currentReport.value) {
      case 'Lịch Hẹn':
        appointmentStats.value = data.data;
        break;
      case 'Doanh Thu':
        revenueStats.value = data.data;
        break;
      case 'Hiệu Suất Bác Sĩ':
        doctorPerformance.value = data.data;
        break;
      case 'Dịch Vụ Phổ Biến':
        servicePopularity.value = data.data;
        break;
      case 'Khách Hàng':
        customerStats.value = data.data;
        break;
    }
  } catch (err) {
    console.error('Lỗi tạo báo cáo:', err);
  }
};
</script>
