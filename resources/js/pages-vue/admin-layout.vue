<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar Navigation -->
    <div class="flex h-screen">
      <nav class="w-64 bg-slate-900 text-white p-6 overflow-y-auto">
        <h1 class="text-2xl font-bold mb-8">PetCare Admin</h1>
        
        <div class="space-y-2">
          <router-link to="/admin/dashboard" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <LayoutDashboard class="w-4 h-4" />
            <span>Bảng điều khiển</span>
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Quản Lý</div>
          <router-link to="/admin/users" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Users class="w-4 h-4" />
            <span>Người Dùng</span>
          </router-link>
          <router-link to="/admin/doctors" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Stethoscope class="w-4 h-4" />
            <span>Bác Sĩ</span>
          </router-link>
          <router-link to="/admin/services" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Activity class="w-4 h-4" />
            <span>Dịch Vụ</span>
          </router-link>
          <router-link to="/admin/pets" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <PawPrint class="w-4 h-4" />
            <span>Thú Cưng</span>
          </router-link>
          <router-link to="/admin/appointments" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Calendar class="w-4 h-4" />
            <span>Lịch Hẹn</span>
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Kho & Thanh Toán</div>
          <router-link to="/admin/medicines" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Pill class="w-4 h-4" />
            <span>Thuốc & Kho</span>
          </router-link>
          <router-link to="/admin/inventory" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Package class="w-4 h-4" />
            <span>Quản Lý Kho</span>
          </router-link>
          <router-link to="/admin/payments" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <CircleDollarSign class="w-4 h-4" />
            <span>Thanh Toán</span>
          </router-link>
          
          <div class="text-xs uppercase text-gray-400 mt-6 mb-2">Báo Cáo & Cấu Hình</div>
          <router-link to="/admin/reports" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <TrendingUp class="w-4 h-4" />
            <span>Báo Cáo</span>
          </router-link>
          <router-link to="/admin/settings" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <Settings class="w-4 h-4" />
            <span>Cấu Hình</span>
          </router-link>
          <router-link to="/admin/logs" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-slate-700" active-class="bg-blue-600">
            <ClipboardList class="w-4 h-4" />
            <span>Nhật Ký</span>
          </router-link>
        </div>

        <div class="border-t border-slate-700 mt-8 pt-4">
          <button @click="logout" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white font-bold transition">
            <LogOut class="w-4 h-4" />
            <span>Đăng Xuất</span>
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
import { LayoutDashboard, Users, Stethoscope, Activity, PawPrint, Calendar, Pill, Package, CircleDollarSign, TrendingUp, Settings, ClipboardList, LogOut } from '@lucide/vue';
import { useNotification } from '../composables/useNotification';

const router = useRouter();
const route = useRoute();
const { handleApiError } = useNotification();
const userInfo = ref({ name: 'Admin' });

const pageTitle = computed(() => {
  const titles = {
    'dashboard': 'Bảng điều khiển',
    'users': 'Quản Lý Người Dùng',
    'doctors': 'Quản Lý Bác Sĩ',
    'services': 'Quản Lý Dịch Vụ',
    'medicines': 'Quản Lý Thuốc',
    'inventory': 'Quản Lý Kho',
    'pets': 'Quản Lý Thú Cưng',
    'appointments': 'Quản Lý Lịch Hẹn',
    'payments': 'Quản Lý Thanh Toán',
    'reports': 'Báo Cáo Thống Kê',
    'settings': 'Cấu Hình Hệ Thống',
    'logs': 'Nhật Ký Hoạt Động',
  };
  return titles[route.name] || 'Admin';
});

onMounted(async () => {
  try {
    const res = await fetch('/api/auth/me', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    userInfo.value = data;
  } catch (err) {
    handleApiError(err);
  }
});

const logout = async () => {
  await fetch('/api/auth/logout', {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
  });
  localStorage.removeItem('petcare_sanctum_token');
  router.push('/auth/login');
};
</script>
