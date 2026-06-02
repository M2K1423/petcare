<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm dịch vụ..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <button @click="openCreateForm" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
        ➕ Thêm Dịch Vụ
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="service in filteredServices" :key="service.id" class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-bold text-lg">{{ service.name }}</h3>
            <p class="text-sm text-gray-600">{{ service.description }}</p>
          </div>
          <button @click="toggleService(service)" :class="[
            'px-3 py-1 rounded text-xs font-semibold',
            service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
          ]">
            {{ service.is_active ? '✓ Bật' : '❌ Tắt' }}
          </button>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
          <div>
            <strong class="text-gray-600">Giá:</strong>
            <div class="text-xl font-bold text-blue-600">{{ formatCurrency(service.price) }}</div>
          </div>
          <div>
            <strong class="text-gray-600">Thời Gian:</strong>
            <div class="text-lg font-bold">{{ service.duration_minutes }}p</div>
          </div>
        </div>

        <div class="flex gap-2">
          <button @click="editService(service)" class="flex-1 text-blue-600 hover:text-blue-800">✏️ Sửa</button>
          <button @click="deleteService(service)" class="flex-1 text-red-600 hover:text-red-800">🗑️ Xóa</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 w-full max-w-2xl">
        <h3 class="text-2xl font-bold mb-6">{{ editingId ? 'Sửa Dịch Vụ' : 'Thêm Dịch Vụ Mới' }}</h3>

        <div class="space-y-4">
          <div>
            <input v-model="form.name" placeholder="Tên Dịch Vụ" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.name}">
            <span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</span>
          </div>
          <div>
            <textarea v-model="form.description" placeholder="Mô Tả" rows="3" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.description}"></textarea>
            <span v-if="errors.description" class="text-red-500 text-xs">{{ errors.description }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <input v-model.number="form.price" type="number" placeholder="Giá" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.price}">
              <span v-if="errors.price" class="text-red-500 text-xs">{{ errors.price }}</span>
            </div>
            <div>
              <input v-model.number="form.duration_minutes" type="number" placeholder="Thời Gian (phút)" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.duration_minutes}">
              <span v-if="errors.duration_minutes" class="text-red-500 text-xs">{{ errors.duration_minutes }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <button @click="saveService" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
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

const services = ref([]);
const search = ref('');
const showForm = ref(false);
const editingId = ref(null);
const errors = ref({});
const form = ref({
  name: '',
  description: '',
  price: 0,
  duration_minutes: 30,
  is_active: true
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const filteredServices = computed(() => {
  if (!search.value) return services.value;
  const q = search.value.toLowerCase();
  return services.value.filter(s =>
    s.name.toLowerCase().includes(q) ||
    s.description?.toLowerCase().includes(q)
  );
});

const isLoading = ref(true);

const fetchServices = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/admin/services', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    services.value = data.data;
  } catch (err) {
    handleApiError(err);
  } finally {
    isLoading.value = false;
  }
};

const openCreateForm = () => {
  editingId.value = null;
  form.value = { name: '', description: '', price: 0, duration_minutes: 30, is_active: true };
  errors.value = {};
  showForm.value = true;
};

const editService = (service) => {
  editingId.value = service.id;
  form.value = { ...service };
  errors.value = {};
  showForm.value = true;
};

const saveService = async () => {
  errors.value = {};
  try {
    const url = editingId.value ? `/api/admin/services/${editingId.value}` : '/api/admin/services';
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
      form.value = { name: '', description: '', price: 0, duration_minutes: 30, is_active: true };
      notifySuccess(method === 'PUT' ? 'Cập nhật dịch vụ thành công!' : 'Đã thêm dịch vụ mới!');
      fetchServices();
    } else {
        errors.value = await handleApiError(null, res);
    }
  } catch (err) {
    errors.value = await handleApiError(err);
  }
};

const toggleService = async (service) => {
  try {
    const res = await fetch(`/api/admin/services/${service.id}/toggle`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (res.ok) {
        notifySuccess(service.is_active ? 'Đã tắt dịch vụ!' : 'Đã bật dịch vụ!');
        fetchServices();
    } else {
        await handleApiError(null, res);
    }
  } catch (err) {
    handleApiError(err);
  }
};

const deleteService = async (service) => {
  if (confirm(`Xác nhận xóa dịch vụ ${service.name}?`)) {
    try {
      const res = await fetch(`/api/admin/services/${service.id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      if (res.ok) {
          notifySuccess('Xóa dịch vụ thành công!');
          fetchServices();
      } else {
          await handleApiError(null, res);
      }
    } catch (err) {
      handleApiError(err);
    }
  }
};

onMounted(fetchServices);
</script>
