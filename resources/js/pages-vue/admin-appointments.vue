<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm lịch hẹn..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <select v-model="statusFilter" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả trạng thái</option>
        <option value="scheduled">Đã Lên Lịch</option>
        <option value="completed">Hoàn Thành</option>
        <option value="cancelled">Đã Hủy</option>
      </select>
      <button @click="refreshAppointments" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
        🔄 Làm Mới
      </button>
    </div>

    <!-- Today's Appointments Summary -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
        <div class="text-sm text-gray-600">Hôm Nay</div>
        <div class="text-3xl font-bold text-blue-600">{{ todayCount }}</div>
      </div>
      <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
        <div class="text-sm text-gray-600">Hoàn Thành</div>
        <div class="text-3xl font-bold text-green-600">{{ completedCount }}</div>
      </div>
      <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
        <div class="text-sm text-gray-600">Đã Lên Lịch</div>
        <div class="text-3xl font-bold text-yellow-600">{{ scheduledCount }}</div>
      </div>
      <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
        <div class="text-sm text-gray-600">Đã Hủy</div>
        <div class="text-3xl font-bold text-red-600">{{ cancelledCount }}</div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Thời Gian</th>
            <th class="px-6 py-3 text-left">Thú Cưng</th>
            <th class="px-6 py-3 text-left">Chủ Nuôi</th>
            <th class="px-6 py-3 text-left">Bác Sĩ</th>
            <th class="px-6 py-3 text-left">Dịch Vụ</th>
            <th class="px-6 py-3 text-left">Trạng Thái</th>
            <th class="px-6 py-3 text-left">Hành Động</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="apt in filteredAppointments" :key="apt.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ formatDateTime(apt.appointment_time) }}</td>
            <td class="px-6 py-4">{{ apt.pet?.name || 'N/A' }}</td>
            <td class="px-6 py-4">{{ apt.owner?.name || 'N/A' }}</td>
            <td class="px-6 py-4">{{ apt.doctor?.user?.name || 'Chưa Gán' }}</td>
            <td class="px-6 py-4">{{ apt.service?.name || 'N/A' }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                apt.status === 'completed' ? 'bg-green-100 text-green-800' :
                apt.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                'bg-blue-100 text-blue-800'
              ]">
                {{ statusLabel(apt.status) }}
              </span>
            </td>
            <td class="px-6 py-4 space-x-2 text-sm">
              <button v-if="!apt.doctor" @click="assignDoctor(apt)" class="text-blue-600 hover:text-blue-800">👨‍⚕️</button>
              <button @click="reschedule(apt)" class="text-orange-600 hover:text-orange-800">📅</button>
              <button v-if="apt.status !== 'cancelled'" @click="cancelAppointment(apt)" class="text-red-600 hover:text-red-800">❌</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const appointments = ref([]);
const search = ref('');
const statusFilter = ref('');

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('vi-VN');
};

const statusLabel = (status) => {
  const labels = {
    'scheduled': 'Đã Lên Lịch',
    'completed': 'Hoàn Thành',
    'cancelled': 'Đã Hủy'
  };
  return labels[status] || status;
};

const filteredAppointments = computed(() => {
  let result = appointments.value;
  if (search.value) {
    const q = search.value.toLowerCase();
    result = result.filter(a =>
      a.pet?.name?.toLowerCase().includes(q) ||
      a.owner?.name?.toLowerCase().includes(q)
    );
  }
  if (statusFilter.value) {
    result = result.filter(a => a.status === statusFilter.value);
  }
  return result;
});

const todayCount = computed(() => {
  const today = new Date().toDateString();
  return appointments.value.filter(a => 
    new Date(a.appointment_time).toDateString() === today
  ).length;
});

const completedCount = computed(() =>
  appointments.value.filter(a => a.status === 'completed').length
);

const scheduledCount = computed(() =>
  appointments.value.filter(a => a.status === 'scheduled').length
);

const cancelledCount = computed(() =>
  appointments.value.filter(a => a.status === 'cancelled').length
);

const fetchAppointments = async () => {
  try {
    const res = await fetch('/api/admin/appointments', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    appointments.value = data.data;
  } catch (err) {
    console.error('Lỗi tải lịch hẹn:', err);
  }
};

const assignDoctor = (apt) => {
  const doctorId = prompt('Nhập ID Bác Sĩ:');
  if (doctorId) {
    fetch(`/api/admin/appointments/${apt.id}/assign-doctor`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ doctor_id: parseInt(doctorId) })
    }).then(() => fetchAppointments());
  }
};

const reschedule = (apt) => {
  const newTime = prompt('Nhập thời gian mới (YYYY-MM-DD HH:MM):');
  if (newTime) {
    fetch(`/api/admin/appointments/${apt.id}/reschedule`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ new_appointment_time: newTime })
    }).then(() => fetchAppointments());
  }
};

const cancelAppointment = (apt) => {
  const reason = prompt('Lý do hủy:');
  if (reason) {
    fetch(`/api/admin/appointments/${apt.id}/cancel`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ reason })
    }).then(() => fetchAppointments());
  }
};

const refreshAppointments = () => {
  fetchAppointments();
};

onMounted(fetchAppointments);
</script>
