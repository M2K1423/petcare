<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold">⚙️ Cấu Hình Hệ Thống</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Clinic Information -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">🏥 Thông Tin Phòng Khám</h3>
        <div class="space-y-4">
          <input v-model="settings.clinic_name" placeholder="Tên Phòng Khám" class="w-full px-4 py-2 border rounded">
          <input v-model="settings.clinic_address" placeholder="Địa Chỉ" class="w-full px-4 py-2 border rounded">
          <input v-model="settings.clinic_phone" placeholder="Điện Thoại" class="w-full px-4 py-2 border rounded">
          <input v-model="settings.clinic_email" type="email" placeholder="Email" class="w-full px-4 py-2 border rounded">
          <textarea v-model="settings.clinic_description" placeholder="Mô Tả" rows="3" class="w-full px-4 py-2 border rounded"></textarea>
        </div>
      </div>

      <!-- Working Hours -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">⏰ Giờ Làm Việc</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Thứ 2 - Thứ 6</label>
            <div class="flex gap-2">
              <input v-model="settings.working_hours_start" type="time" class="flex-1 px-4 py-2 border rounded">
              <input v-model="settings.working_hours_end" type="time" class="flex-1 px-4 py-2 border rounded">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Thứ 7</label>
            <div class="flex gap-2">
              <input v-model="settings.weekend_start" type="time" class="flex-1 px-4 py-2 border rounded">
              <input v-model="settings.weekend_end" type="time" class="flex-1 px-4 py-2 border rounded">
            </div>
          </div>
        </div>
      </div>

      <!-- Appointment Settings -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">📅 Cấu Hình Lịch Hẹn</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Thời Gian Slot Lịch (phút)</label>
            <input v-model.number="settings.appointment_slot_minutes" type="number" class="w-full px-4 py-2 border rounded">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Max Lịch Hẹn/Ngày</label>
            <input v-model.number="settings.max_appointments_per_day" type="number" class="w-full px-4 py-2 border rounded">
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input v-model="settings.allow_online_booking" type="checkbox" class="w-4 h-4">
              <span>Cho Phép Đặt Lịch Online</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Payment Settings -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">💳 Thanh Toán</h3>
        <div class="space-y-4">
          <label class="flex items-center gap-2">
            <input v-model="settings.enable_online_payment" type="checkbox" class="w-4 h-4">
            <span>Bật Thanh Toán Online</span>
          </label>
          <label class="flex items-center gap-2">
            <input v-model="settings.enable_card_payment" type="checkbox" class="w-4 h-4">
            <span>Chấp Nhận Thẻ</span>
          </label>
          <label class="flex items-center gap-2">
            <input v-model="settings.enable_transfer_payment" type="checkbox" class="w-4 h-4">
            <span>Chấp Nhận Chuyển Khoản</span>
          </label>
          <input v-model="settings.bank_account_name" placeholder="Tên Tài Khoản" class="w-full px-4 py-2 border rounded">
          <input v-model="settings.bank_account_number" placeholder="Số Tài Khoản" class="w-full px-4 py-2 border rounded">
        </div>
      </div>

      <!-- Notification Settings -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">🔔 Thông Báo</h3>
        <div class="space-y-4">
          <label class="flex items-center gap-2">
            <input v-model="settings.send_appointment_reminders" type="checkbox" class="w-4 h-4">
            <span>Nhắc Nhở Lịch Hẹn</span>
          </label>
          <label class="flex items-center gap-2">
            <input v-model="settings.send_vaccination_reminders" type="checkbox" class="w-4 h-4">
            <span>Nhắc Nhở Tiêm Chủng</span>
          </label>
          <label class="flex items-center gap-2">
            <input v-model="settings.send_payment_reminders" type="checkbox" class="w-4 h-4">
            <span>Nhắc Nhở Thanh Toán</span>
          </label>
          <div>
            <label class="block text-sm font-medium mb-1">Gửi Thông Báo Trước (giờ)</label>
            <input v-model.number="settings.reminder_hours_before" type="number" class="w-full px-4 py-2 border rounded">
          </div>
        </div>
      </div>

      <!-- System Settings -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">⚙️ Hệ Thống</h3>
        <div class="space-y-4">
          <label class="flex items-center gap-2">
            <input v-model="settings.enable_activity_logging" type="checkbox" class="w-4 h-4">
            <span>Ghi Nhật Ký Hoạt Động</span>
          </label>
          <label class="flex items-center gap-2">
            <input v-model="settings.enable_backup" type="checkbox" class="w-4 h-4">
            <span>Bật Sao Lưu Tự Động</span>
          </label>
          <div>
            <label class="block text-sm font-medium mb-1">Tần Suất Sao Lưu (giờ)</label>
            <input v-model.number="settings.backup_frequency_hours" type="number" class="w-full px-4 py-2 border rounded">
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex gap-4">
      <button @click="saveSettings" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
        💾 Lưu Cấu Hình
      </button>
      <button @click="resetSettings" class="flex-1 bg-gray-300 py-3 rounded-lg hover:bg-gray-400 font-semibold">
        🔄 Đặt Lại
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const settings = ref({
  clinic_name: '',
  clinic_address: '',
  clinic_phone: '',
  clinic_email: '',
  clinic_description: '',
  working_hours_start: '08:00',
  working_hours_end: '17:00',
  weekend_start: '09:00',
  weekend_end: '12:00',
  appointment_slot_minutes: 30,
  max_appointments_per_day: 20,
  allow_online_booking: true,
  enable_online_payment: true,
  enable_card_payment: true,
  enable_transfer_payment: true,
  bank_account_name: '',
  bank_account_number: '',
  send_appointment_reminders: true,
  send_vaccination_reminders: true,
  send_payment_reminders: true,
  reminder_hours_before: 24,
  enable_activity_logging: true,
  enable_backup: true,
  backup_frequency_hours: 24
});

const fetchSettings = async () => {
  try {
    const res = await fetch('/api/admin/settings', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    Object.assign(settings.value, data.data);
  } catch (err) {
    console.error('Lỗi tải cấu hình:', err);
  }
};

const saveSettings = async () => {
  try {
    await fetch('/api/admin/settings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify(settings.value)
    });
    alert('✓ Lưu cấu hình thành công!');
  } catch (err) {
    console.error('Lỗi lưu cấu hình:', err);
    alert('❌ Lỗi lưu cấu hình!');
  }
};

const resetSettings = () => {
  if (confirm('Bạn có chắc chắn muốn đặt lại cấu hình?')) {
    fetchSettings();
  }
};

onMounted(fetchSettings);
</script>
