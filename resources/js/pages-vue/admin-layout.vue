<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar Navigation -->
    <div class="flex h-screen">
      <nav class="w-64 bg-slate-900 text-white p-6 overflow-y-auto">
        <h1 class="text-2xl font-bold mb-8">PetCare Admin</h1>
        
        <div class="space-y-2">
          <router-link to="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            📊 Dashboard
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Quản Lý</div>
          <router-link to="/admin/users" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            👥 Người Dùng
          </router-link>
          <router-link to="/admin/doctors" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            🏥 Bác Sĩ
          </router-link>
          <router-link to="/admin/services" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            ⚕️ Dịch Vụ
          </router-link>
          <router-link to="/admin/pets" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            🐾 Thú Cưng
          </router-link>
          <router-link to="/admin/appointments" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            📅 Lịch Hẹn
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Kho & Thanh Toán</div>
          <router-link to="/admin/medicines" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            💊 Thuốc & Kho
          </router-link>
          <router-link to="/admin/inventory" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            📦 Quản Lý Kho
          </router-link>
          <router-link to="/admin/payments" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            💰 Thanh Toán
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Báo Cáo & Cấu Hình</div>
          <router-link to="/admin/reports" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            📈 Báo Cáo
          </router-link>
          <router-link to="/admin/settings" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            ⚙️ Cấu Hình
          </router-link>
          <router-link to="/admin/logs" class="block px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            📋 Nhật Ký
          </router-link>
        </div>

        <div class="border-t border-slate-700 mt-8 pt-4">
          <button @click="logout" class="w-full px-4 py-2 rounded bg-red-600 hover:bg-red-700">
            🚪 Đăng Xuất
          </button>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="flex-1 overflow-auto">
        <header class="bg-white shadow p-6 flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h2>
          <div class="text-gray-600">{{ userInfo.name }}</div>
        </header>
        
        <div class="p-6">
          <router-view></router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const userInfo = ref({ name: 'Admin' });

const pageTitle = computed(() => {
  const titles = {
    'dashboard': '📊 Dashboard',
    'users': '👥 Quản Lý Người Dùng',
    'doctors': '🏥 Quản Lý Bác Sĩ',
    'services': '⚕️ Quản Lý Dịch Vụ',
    'medicines': '💊 Quản Lý Thuốc',
    'inventory': '📦 Quản Lý Kho',
    'pets': '🐾 Quản Lý Thú Cưng',
    'appointments': '📅 Quản Lý Lịch Hẹn',
    'payments': '💰 Quản Lý Thanh Toán',
    'reports': '📈 Báo Cáo Thống Kê',
    'settings': '⚙️ Cấu Hình Hệ Thống',
    'logs': '📋 Nhật Ký Hoạt Động',
  };
  return titles[route.name] || 'Admin';
});

onMounted(async () => {
  try {
    const res = await fetch('/api/auth/me', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    userInfo.value = data;
  } catch (err) {
    console.error(err);
  }
});

const logout = async () => {
  await fetch('/api/auth/logout', {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
  });
  localStorage.removeItem('token');
  router.push('/auth/login');
};
</script>
