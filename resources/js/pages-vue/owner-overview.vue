<template>
  <template v-if="!isLoading">
    <!-- Section 1: Welcome Header Banner with Elegant HSL Gradient & Greeting -->
    <header class="welcome-banner mb-8 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
      <div class="banner-pattern"></div>
      <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md text-white mb-4 animate-pulse-subtle">
          <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
          Trợ lý Ảo AI PetCare Đang Hoạt Động
        </span>
        <h1 class="text-3xl font-extrabold tracking-tight md:text-4xl flex items-center gap-2">
          <Sun v-if="greetingTime === 'morning'" class="w-8 h-8 text-amber-300" />
          <CloudSun v-else-if="greetingTime === 'afternoon'" class="w-8 h-8 text-orange-200" />
          <Moon v-else class="w-8 h-8 text-indigo-200" />
          <span>{{ greetingText }}, </span>
          <span class="text-amber-200">{{ currentUser.name }}</span>!
        </h1>
        <p class="mt-2 text-white/80 max-w-xl text-sm md:text-base">
          Chào mừng bạn quay trở lại với phòng khám thú y thông minh PetCare. Hãy theo dõi sức khỏe và lịch khám của các bé cưng ngay dưới đây nhé!
        </p>
      </div>
    </header>

    <!-- Section 2: Vivid & Interactive Glassmorphism Statistics Cards -->
    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Card 1: Pets -->
      <article class="stat-card group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 relative overflow-hidden">
        <div class="card-glow bg-indigo-500/5"></div>
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Thú cưng yêu quý</p>
            <p class="mt-4 text-5xl font-extrabold tracking-tight text-slate-800 transition-colors group-hover:text-indigo-600">{{ pets.length }}</p>
          </div>
          <div class="rounded-2xl bg-indigo-50 p-4 text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
            <PawPrint class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-4 text-xs text-slate-400">Các hồ sơ sức khỏe và lịch sử khám đang hoạt động.</p>
      </article>

      <!-- Card 2: Total Appointments -->
      <article class="stat-card group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 relative overflow-hidden">
        <div class="card-glow bg-amber-500/5"></div>
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Lịch hẹn đã đặt</p>
            <p class="mt-4 text-5xl font-extrabold tracking-tight text-slate-800 transition-colors group-hover:text-amber-500">{{ appointments.length }}</p>
          </div>
          <div class="rounded-2xl bg-amber-50 p-4 text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
            <Calendar class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-4 text-xs text-slate-400">Tổng quan lịch hẹn khám bệnh và chăm sóc.</p>
      </article>

      <!-- Card 3: Pending Appointments -->
      <article class="stat-card group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 relative overflow-hidden">
        <div class="card-glow bg-rose-500/5"></div>
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Đang chờ xác nhận</p>
            <p class="mt-4 text-5xl font-extrabold tracking-tight text-slate-800 transition-colors group-hover:text-rose-500">{{ pendingCount }}</p>
          </div>
          <div class="rounded-2xl bg-rose-50 p-4 text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 relative">
            <span v-if="pendingCount > 0" class="absolute top-1 right-1 flex h-3.5 w-3.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-rose-500 border border-white"></span>
            </span>
            <Hourglass class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-4 text-xs text-slate-400">Các yêu cầu xếp lịch đang được lễ tân xử lý.</p>
      </article>
    </section>

    <!-- Section 3: Dynamic Action Center & Active Reminders -->
    <section class="mt-8 grid gap-8 lg:grid-cols-5">
      <!-- Left side: Quick Actions list (Takes 2/5 columns) -->
      <article class="lg:col-span-2 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.01)] flex flex-col justify-between">
        <div>
          <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
            <span class="flex h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
            Thao tác nhanh
          </h2>
          <p class="text-xs text-slate-400 mt-1">Truy cập nhanh các nghiệp vụ chăm sóc thú cưng.</p>
        </div>
        
        <div class="mt-6 space-y-3.5">
          <!-- Button 1 -->
          <a href="/owner/pets" class="quick-action-btn flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-indigo-50/40 hover:border-indigo-100 transition-all duration-300 group">
            <div class="flex items-center gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform duration-300">
                <PawPrint class="w-5 h-5" />
              </div>
              <div class="text-left">
                <p class="text-sm font-bold text-slate-700">Quản lý Thú cưng</p>
                <p class="text-xs text-slate-400">Thêm mới hoặc sửa hồ sơ các bé</p>
              </div>
            </div>
            <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-500 transition-all duration-300">
              <ChevronRight class="h-5 w-5" />
            </span>
          </a>

          <!-- Button 2 -->
          <a href="/owner/appointments" class="quick-action-btn flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-amber-50/40 hover:border-amber-100 transition-all duration-300 group">
            <div class="flex items-center gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform duration-300">
                <Calendar class="w-5 h-5" />
              </div>
              <div class="text-left">
                <p class="text-sm font-bold text-slate-700">Đặt lịch hẹn mới</p>
                <p class="text-xs text-slate-400">Đặt bác sĩ & thời gian khám nhanh chóng</p>
              </div>
            </div>
            <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-amber-500 transition-all duration-300">
              <ChevronRight class="h-5 w-5" />
            </span>
          </a>

          <!-- Button 3 -->
          <a href="/owner/shop" class="quick-action-btn flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-emerald-50/40 hover:border-emerald-100 transition-all duration-300 group">
            <div class="flex items-center gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg group-hover:scale-110 transition-transform duration-300">
                <Pill class="w-5 h-5" />
              </div>
              <div class="text-left">
                <p class="text-sm font-bold text-slate-700">Cửa hàng thuốc y tế</p>
                <p class="text-xs text-slate-400">Xem thuốc & các thực phẩm chức năng</p>
              </div>
            </div>
            <span class="text-slate-400 group-hover:translate-x-1 group-hover:text-emerald-500 transition-all duration-300">
              <ChevronRight class="h-5 w-5" />
            </span>
          </a>
        </div>
      </article>

      <!-- Right side: Recent Appointments List (Takes 3/5 columns) -->
      <article class="lg:col-span-3 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.01)]">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
              <span class="flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
              Lịch khám gần đây
            </h2>
            <p class="text-xs text-slate-400 mt-1">Thông tin chi tiết các lịch khám bệnh gần nhất của bạn.</p>
          </div>
          <span class="text-xs font-semibold px-2.5 py-1 bg-slate-50 text-slate-500 rounded-full border border-slate-100">Cập nhật liên tục</span>
        </div>

        <div class="mt-6 space-y-3 max-h-[300px] overflow-y-auto pr-1">
          <div v-if="appointments.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
            <CalendarOff class="w-10 h-10 text-slate-300 mb-2" />
            <p class="text-sm font-semibold text-slate-500">Bạn chưa có lịch hẹn khám nào.</p>
            <a href="/owner/appointments" class="mt-2 text-xs font-bold text-indigo-500 hover:underline">Đặt lịch ngay hôm nay!</a>
          </div>
          
          <div v-for="item in recentAppointments" :key="item.id" class="appointment-item flex items-center justify-between p-4 rounded-2xl border border-slate-50 bg-[#FCFDFE] hover:border-slate-200 transition-all duration-300">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-slate-100/80 flex items-center justify-center">
                <PawPrint class="w-5 h-5 text-slate-400" />
              </div>
              <div>
                <p class="text-sm font-bold text-slate-700">{{ item.pet?.name ?? 'Chưa xác định' }}</p>
                <p class="text-xs text-slate-400 flex items-center gap-1.5 mt-0.5">
                  <Clock class="h-3.5 w-3.5 text-slate-400" />
                  {{ formatDateTime(item.appointment_at) }}
                </p>
              </div>
            </div>
            
            <span :class="getStatusClass(item.status)" class="text-xs font-bold px-3 py-1.5 rounded-full border">
              {{ getStatusLabel(item.status) }}
            </span>
          </div>
        </div>
      </article>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { ChevronRight, Clock, Calendar, PawPrint, Hourglass, Pill, CalendarOff, Sun, CloudSun, Moon } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const pets = ref([]);
const appointments = ref([]);
const currentUser = ref({ name: 'Chủ nuôi' });
const isLoading = ref(true);

const pendingCount = computed(() => appointments.value.filter(a => a.status === 'pending').length);
const recentAppointments = computed(() => appointments.value.slice(0, 5));

// Dynamic Greeting based on time
const greetingTime = computed(() => {
  const hr = new Date().getHours();
  if (hr < 12) return 'morning';
  if (hr < 18) return 'afternoon';
  return 'evening';
});

const greetingText = computed(() => {
  switch (greetingTime.value) {
    case 'morning': return 'Chào buổi sáng';
    case 'afternoon': return 'Chào buổi chiều';
    default: return 'Chào buổi tối';
  }
});

function formatDateTime(input) {
  const date = new Date(input);
  if (Number.isNaN(date.getTime())) return input;
  return date.toLocaleString('vi-VN', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function getStatusLabel(status) {
  switch (status) {
    case 'pending': return 'Chờ duyệt';
    case 'confirmed': return 'Đã xác nhận';
    case 'completed': return 'Đã xong';
    case 'cancelled': return 'Đã hủy';
    default: return status;
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'bg-amber-50/50 text-amber-600 border-amber-100';
    case 'confirmed': return 'bg-sky-50/50 text-sky-600 border-sky-100';
    case 'completed': return 'bg-emerald-50/50 text-emerald-600 border-emerald-100';
    case 'cancelled': return 'bg-rose-50/50 text-rose-600 border-rose-100';
    default: return 'bg-slate-50/50 text-slate-600 border-slate-100';
  }
}

async function bootstrap() {
  isLoading.value = true;
  try {
    const [petsResponse, appointmentsResponse, meResponse] = await Promise.all([
      callApi('/api/owner/pets', 'GET'),
      callApi('/api/owner/appointments', 'GET'),
      callApi('/api/auth/me', 'GET'),
    ]);

    pets.value = petsResponse.data || [];
    appointments.value = appointmentsResponse.data || [];
    if (meResponse) {
      currentUser.value = meResponse.user || meResponse;
    }
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
}

onMounted(() => {
  bootstrap();
});
</script>

<style scoped>
.welcome-banner {
  background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 50%, #4299e1 100%);
  background-size: 200% 200%;
  animation: gradientFlow 10s ease infinite;
}

.banner-pattern {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0.08;
  background-image: radial-gradient(#ffffff 2px, transparent 2px);
  background-size: 24px 24px;
}

@keyframes gradientFlow {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes pulse-subtle {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.85; transform: scale(0.98); }
}

.animate-pulse-subtle {
  animation: pulse-subtle 3s ease-in-out infinite;
}

.card-glow {
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  transform: rotate(-45deg);
  filter: blur(40px);
  pointer-events: none;
  transition: all 0.5s ease;
}

.stat-card:hover .card-glow {
  transform: scale(1.3) rotate(-30deg);
}

.quick-action-btn:hover {
  transform: translateX(4px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}

.appointment-item {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.appointment-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03);
}

::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
