<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm bác sĩ..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <button @click="openCreateForm" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
        ➕ Thêm Bác Sĩ
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="doctor in filteredDoctors" :key="doctor.id" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-bold text-lg">{{ doctor.user?.name || 'N/A' }}</h3>
            <p class="text-sm text-gray-600">{{ doctor.specialty }}</p>
          </div>
          <span :class="[
            'px-2 py-1 rounded text-xs font-semibold',
            doctor.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
          ]">
            {{ doctor.is_active ? '✓ Hoạt Động' : '❌ Không Hoạt Động' }}
          </span>
        </div>

        <div class="space-y-2 text-sm mb-4">
          <div><strong>Bằng Cấp:</strong> {{ doctor.license_number }}</div>
          <div><strong>Kinh Nghiệm:</strong> {{ doctor.years_of_experience }} năm</div>
          <div><strong>Điện Thoại:</strong> {{ doctor.phone }}</div>
        </div>

        <div class="flex gap-2">
          <button @click="editDoctor(doctor)" class="flex-1 text-blue-600 hover:text-blue-800">✏️ Sửa</button>
          <button @click="deleteDoctor(doctor)" class="flex-1 text-red-600 hover:text-red-800">🗑️ Xóa</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 w-full max-w-2xl">
        <h3 class="text-2xl font-bold mb-6">{{ editingId ? 'Sửa Bác Sĩ' : 'Thêm Bác Sĩ Mới' }}</h3>

        <div class="space-y-4">
          <div>
            <input v-model="form.license_number" placeholder="Bằng Cấp" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.license_number}">
            <span v-if="errors.license_number" class="text-red-500 text-xs">{{ errors.license_number }}</span>
          </div>
          <div>
            <input v-model="form.specialty" placeholder="Chuyên Khoa" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.specialty}">
            <span v-if="errors.specialty" class="text-red-500 text-xs">{{ errors.specialty }}</span>
          </div>
          <div>
            <input v-model.number="form.years_of_experience" type="number" placeholder="Năm Kinh Nghiệm" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.years_of_experience}">
            <span v-if="errors.years_of_experience" class="text-red-500 text-xs">{{ errors.years_of_experience }}</span>
          </div>
          <div>
            <input v-model="form.phone" placeholder="Điện Thoại" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.phone}">
            <span v-if="errors.phone" class="text-red-500 text-xs">{{ errors.phone }}</span>
          </div>
          <div>
            <input v-model="form.email" type="email" placeholder="Email" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.email}">
            <span v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</span>
          </div>

          <div class="flex gap-2">
            <button @click="saveDoctor" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
              💾 Lưu
            </button>
            <button @click="showForm = false" class="flex-1 bg-gray-300 py-2 rounded hover:bg-gray-400">
              Hủy
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const doctors = ref([]);
const search = ref('');
const showForm = ref(false);
const editingId = ref(null);
const errors = ref({});
const form = ref({
  license_number: '',
  specialty: '',
  years_of_experience: 0,
  phone: '',
  email: ''
});

const filteredDoctors = computed(() => {
  if (!search.value) return doctors.value;
  const q = search.value.toLowerCase();
  return doctors.value.filter(d =>
    d.user?.name?.toLowerCase().includes(q) ||
    d.specialty.toLowerCase().includes(q) ||
    d.license_number.toLowerCase().includes(q)
  );
});

const isLoading = ref(true);

const fetchDoctors = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/admin/doctors', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    doctors.value = data.data;
  } catch (err) {
    handleApiError(err);
  } finally {
    isLoading.value = false;
  }
};

const openCreateForm = () => {
  editingId.value = null;
  form.value = { license_number: '', specialty: '', years_of_experience: 0, phone: '', email: '' };
  errors.value = {};
  showForm.value = true;
};

const editDoctor = (doctor) => {
  editingId.value = doctor.id;
  form.value = { ...doctor };
  errors.value = {};
  showForm.value = true;
};

const saveDoctor = async () => {
  errors.value = {};
  try {
    const url = editingId.value ? `/api/admin/doctors/${editingId.value}` : '/api/admin/doctors';
    const method = editingId.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify(form.value)
    });

    if (res.ok) {
      showForm.value = false;
      editingId.value = null;
      form.value = { license_number: '', specialty: '', years_of_experience: 0, phone: '', email: '' };
      notifySuccess(method === 'PUT' ? 'Cập nhật bác sĩ thành công!' : 'Thêm bác sĩ thành công!');
      fetchDoctors();
    } else {
        errors.value = await handleApiError(null, res);
    }
  } catch (err) {
    errors.value = await handleApiError(err);
  }
};

const deleteDoctor = async (doctor) => {
  if (confirm(`Xác nhận xóa bác sĩ ${doctor.user?.name}?`)) {
    try {
      const res = await fetch(`/api/admin/doctors/${doctor.id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (res.ok) {
          notifySuccess('Xóa bác sĩ thành công!');
          fetchDoctors();
      } else {
          await handleApiError(null, res);
      }
    } catch (err) {
      handleApiError(err);
    }
  }
};

onMounted(fetchDoctors);
</script>
