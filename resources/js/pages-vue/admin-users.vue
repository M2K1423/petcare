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
        <option value="cashier">Thu ngân</option>
        <option value="technician">Kỹ thuật viên</option>
        <option value="owner">Chủ nuôi</option>
      </select>

      <button @click="openCreateModal" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        ➕ Thêm Người Dùng
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
                'px-3 py-1 rounded-full text-xs font-semibold',
                user.is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
              ]">
                {{ user.is_locked ? '🔒 Khóa' : '✓ Hoạt Động' }}
              </span>
            </td>
            <td class="px-6 py-4 space-x-2">
              <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800 text-sm">✏️ Sửa</button>
              <button @click="toggleLock(user)" :class="[
                'text-sm',
                user.is_locked ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'
              ]">
                {{ user.is_locked ? '🔓 Mở' : '🔒 Khóa' }}
              </button>
              <button @click="resetPassword(user)" class="text-orange-600 hover:text-orange-800 text-sm">🔑 Reset MK</button>
              <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800 text-sm">🗑️ Xóa</button>
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
          <input v-model="form.name" type="text" placeholder="Tên" class="w-full px-4 py-2 border rounded">
          <input v-model="form.email" type="email" placeholder="Email" class="w-full px-4 py-2 border rounded">
          <input v-model="form.phone" type="text" placeholder="Điện thoại" class="w-full px-4 py-2 border rounded">
          
          <select v-model="form.role_id" class="w-full px-4 py-2 border rounded">
            <option value="">Chọn Role</option>
            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>

          <input v-if="!editingUser" v-model="form.password" type="password" placeholder="Mật khẩu" class="w-full px-4 py-2 border rounded">

          <div class="flex gap-2">
            <button @click="saveUser" class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
              💾 Lưu
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

const searchQuery = ref('');
const filterRole = ref('');
const currentPage = ref(1);
const users = ref([]);
const roles = ref([]);
const showModal = ref(false);
const editingUser = ref(null);
const form = ref({ name: '', email: '', phone: '', role_id: '', password: '' });

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

const fetchUsers = async () => {
  try {
    const res = await fetch('/api/admin/users', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    users.value = data.data;
  } catch (err) {
    console.error('Lỗi tải người dùng:', err);
  }
};

const fetchRoles = async () => {
  try {
    const res = await fetch('/api/admin/users?role=admin', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    // Fetch roles from another endpoint or hardcode them
    roles.value = [
      { id: 1, name: 'Admin' },
      { id: 2, name: 'Bác Sĩ' },
      { id: 3, name: 'Lễ Tân' },
      { id: 4, name: 'Thu Ngân' },
      { id: 5, name: 'Kỹ Thuật Viên' },
      { id: 6, name: 'Chủ Nuôi' },
    ];
  } catch (err) {
    console.error('Lỗi tải roles:', err);
  }
};

const openCreateModal = () => {
  editingUser.value = null;
  form.value = { name: '', email: '', phone: '', role_id: '', password: '' };
  showModal.value = true;
};

const editUser = (user) => {
  editingUser.value = user;
  form.value = { ...user, role_id: user.role.id };
  showModal.value = true;
};

const saveUser = async () => {
  try {
    const url = editingUser.value 
      ? `/api/admin/users/${editingUser.value.id}`
      : '/api/admin/users';
    
    const method = editingUser.value ? 'PUT' : 'POST';
    
    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify(form.value)
    });
    
    if (res.ok) {
      showModal.value = false;
      fetchUsers();
    }
  } catch (err) {
    console.error('Lỗi lưu người dùng:', err);
  }
};

const toggleLock = async (user) => {
  try {
    const endpoint = user.is_locked ? 'unlock' : 'lock';
    await fetch(`/api/admin/users/${user.id}/${endpoint}`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    fetchUsers();
  } catch (err) {
    console.error('Lỗi thay đổi trạng thái khóa:', err);
  }
};

const resetPassword = async (user) => {
  const newPassword = prompt('Nhập mật khẩu mới:');
  if (newPassword) {
    try {
      await fetch(`/api/admin/users/${user.id}/reset-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ password: newPassword })
      });
      alert('Reset mật khẩu thành công!');
    } catch (err) {
      console.error('Lỗi reset mật khẩu:', err);
    }
  }
};

const deleteUser = async (user) => {
  if (confirm(`Xác nhận xóa người dùng ${user.name}?`)) {
    try {
      await fetch(`/api/admin/users/${user.id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      fetchUsers();
    } catch (err) {
      console.error('Lỗi xóa người dùng:', err);
    }
  }
};

onMounted(() => {
  fetchUsers();
  fetchRoles();
});
</script>
