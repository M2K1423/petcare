<template>
  <div class="space-y-6">
    <!-- Toolbar -->
    <div class="flex gap-4 items-center">
      <input v-model="searchQuery" type="text" placeholder="Tìm kiếm người dùng..." 
        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      
      <select v-model="filterRole" class="px-4 py-2 border rounded-lg focus:outline-none">
        <option value="">Tất cả role</option>
        <option value="admin">Admin</option>
        <option value="vet">Bác sĩ</option>
        <option value="receptionist">Lễ tân</option>
        <option value="owner">Chủ nuôi</option>
      </select>

      <button @click="openCreateModal" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        <Plus class="w-4 h-4" />
        Thêm Người Dùng
      </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold">Tên</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Điện Thoại</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Role</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Trạng Thái</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Hành Động</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
            <td class="px-6 py-4 text-sm">{{ user.phone }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-semibold',
                user.role.slug === 'admin' ? 'bg-red-100 text-red-800' :
                user.role.slug === 'vet' ? 'bg-blue-100 text-blue-800' :
                user.role.slug === 'receptionist' ? 'bg-green-100 text-green-800' :
                'bg-gray-100 text-gray-800'
              ]">
                {{ user.role.name }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span :class="[
                'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold',
                user.is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
              ]">
                <Lock v-if="user.is_locked" class="w-3 h-3" />
                <Check v-else class="w-3 h-3" />
                {{ user.is_locked ? 'Khóa' : 'Hoạt Động' }}
              </span>
            </td>
            <td class="px-6 py-4 space-x-2">
              <button @click="editUser(user)" class="inline-flex items-center gap-0.5 text-blue-600 hover:text-blue-800 text-sm">
                <Pencil class="w-3.5 h-3.5" />
                Sửa
              </button>
              <button @click="toggleLock(user)" :class="[
                'inline-flex items-center gap-0.5 text-sm',
                user.is_locked ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'
              ]">
                <Unlock v-if="user.is_locked" class="w-3.5 h-3.5" />
                <Lock v-else class="w-3.5 h-3.5" />
                {{ user.is_locked ? 'Mở' : 'Khóa' }}
              </button>
              <button @click="resetPassword(user)" class="inline-flex items-center gap-0.5 text-orange-600 hover:text-orange-800 text-sm">
                <Key class="w-3.5 h-3.5" />
                Reset MK
              </button>
              <button @click="deleteUser(user)" class="inline-flex items-center gap-0.5 text-red-600 hover:text-red-800 text-sm">
                <Trash2 class="w-3.5 h-3.5" />
                Xóa
              </button>
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

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-bold mb-4">{{ editingUser ? 'Sửa Người Dùng' : 'Thêm Người Dùng Mới' }}</h3>
        
        <div class="space-y-4">
          <div>
            <input v-model="form.name" type="text" placeholder="Tên" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.name}">
            <span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</span>
          </div>
          <div>
            <input v-model="form.email" type="email" placeholder="Email" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.email}">
            <span v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</span>
          </div>
          <div>
            <input v-model="form.phone" type="text" placeholder="Điện thoại" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.phone}">
            <span v-if="errors.phone" class="text-red-500 text-xs">{{ errors.phone }}</span>
          </div>
          
          <div>
            <select v-model="form.role_id" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.role_id}">
              <option value="">Chọn Role</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
            <span v-if="errors.role_id" class="text-red-500 text-xs">{{ errors.role_id }}</span>
          </div>

          <div v-if="!editingUser">
            <input v-model="form.password" type="password" placeholder="Mật khẩu" class="w-full px-4 py-2 border rounded" :class="{'border-red-500': errors.password}">
            <span v-if="errors.password" class="text-red-500 text-xs">{{ errors.password }}</span>
          </div>

          <div class="flex gap-2">
            <button @click="saveUser" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
              <Save class="w-4 h-4" />
              Lưu
            </button>
            <button @click="showModal = false" class="flex-1 bg-gray-300 py-2 rounded hover:bg-gray-400">
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
import { Plus, Lock, Unlock, Check, Pencil, Key, Trash2, Save } from '@lucide/vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

let csrfReady = false;

const getCookieValue = (name) => {
  const cookies = document.cookie ? document.cookie.split('; ') : [];
  for (const cookie of cookies) {
    const [key, ...parts] = cookie.split('=');
    if (key === name) {
      return decodeURIComponent(parts.join('='));
    }
  }
  return '';
};

const apiFetch = async (url, options = {}) => {
  const method = options.method || 'GET';
  if (method !== 'GET' && !csrfReady) {
    await fetch('/sanctum/csrf-cookie', {
      method: 'GET',
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    csrfReady = true;
  }
  const xsrfToken = getCookieValue('XSRF-TOKEN');
  
  const headers = {
    ...options.headers,
    ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
  };
  
  return fetch(url, {
    ...options,
    headers,
    credentials: 'same-origin',
  });
};

const searchQuery = ref('');
const filterRole = ref('');
const currentPage = ref(1);
const users = ref([]);
const roles = ref([]);
const showModal = ref(false);
const editingUser = ref(null);
const form = ref({ name: '', email: '', phone: '', role_id: '', password: '' });
const errors = ref({});

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / 10));

const filteredUsers = computed(() => {
  let result = users.value;
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(u => 
      u.name.toLowerCase().includes(q) || 
      u.email.toLowerCase().includes(q)
    );
  }
  
  if (filterRole.value) {
    result = result.filter(u => u.role.slug === filterRole.value);
  }
  
  return result.slice((currentPage.value - 1) * 10, currentPage.value * 10);
});

const isLoading = ref(true);

const fetchUsers = async () => {
  isLoading.value = true;
  try {
    const res = await apiFetch('/api/admin/users', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    users.value = data.data;
  } catch (err) {
    handleApiError(err);
  } finally {
    isLoading.value = false;
  }
};

const fetchRoles = () => {
    roles.value = [
      { id: 4, name: 'Admin' },
      { id: 2, name: 'Bác Sĩ' },
      { id: 3, name: 'Lễ Tân' },
      { id: 1, name: 'Chủ Nuôi' },
    ];
};

const openCreateModal = () => {
  editingUser.value = null;
  form.value = { name: '', email: '', phone: '', role_id: '', password: '' };
  errors.value = {};
  showModal.value = true;
};

const editUser = (user) => {
  editingUser.value = user;
  form.value = { ...user, role_id: user.role.id };
  errors.value = {};
  showModal.value = true;
};

const saveUser = async () => {
  errors.value = {};
  try {
    const url = editingUser.value 
      ? `/api/admin/users/${editingUser.value.id}`
      : '/api/admin/users';
    
    const method = editingUser.value ? 'PUT' : 'POST';
    
    const res = await apiFetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}`
      },
      body: JSON.stringify(form.value)
    });
    
    if (res.ok) {
      showModal.value = false;
      notifySuccess(editingUser.value ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
      fetchUsers();
    } else {
      errors.value = await handleApiError(null, res);
    }
  } catch (err) {
    errors.value = await handleApiError(err);
  }
};

const toggleLock = async (user) => {
  try {
    const endpoint = user.is_locked ? 'unlock' : 'lock';
    const res = await apiFetch(`/api/admin/users/${user.id}/${endpoint}`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (res.ok) {
        notifySuccess(user.is_locked ? 'Đã mở khóa tài khoản!' : 'Đã khóa tài khoản!');
        fetchUsers();
    } else {
        await handleApiError(null, res);
    }
  } catch (err) {
    handleApiError(err);
  }
};

const resetPassword = async (user) => {
  const newPassword = prompt('Nhập mật khẩu mới:');
  if (newPassword) {
    try {
      const res = await apiFetch(`/api/admin/users/${user.id}/reset-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}`
        },
        body: JSON.stringify({ password: newPassword })
      });
      if (res.ok) {
          notifySuccess('Reset mật khẩu thành công!');
      } else {
          await handleApiError(null, res);
      }
    } catch (err) {
      handleApiError(err);
    }
  }
};

const deleteUser = async (user) => {
  if (confirm(`Xác nhận xóa người dùng ${user.name}?`)) {
    try {
      const res = await apiFetch(`/api/admin/users/${user.id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
      });
      if (res.ok) {
          notifySuccess('Xóa người dùng thành công!');
          fetchUsers();
      } else {
          await handleApiError(null, res);
      }
    } catch (err) {
      handleApiError(err);
    }
  }
};

onMounted(() => {
  fetchUsers();
  fetchRoles();
});
</script>
