<template>
  <template v-if="!isLoading">
    <!-- Page Header with Modern Actions Bar -->
    <header class="mb-8 flex flex-col gap-5 md:flex-row md:items-center md:justify-between border-b border-slate-100 pb-5">
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 mb-2">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
          Hệ thống điều phối phòng khám PetCare
        </span>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 flex items-center gap-2">
          <Briefcase class="w-6 h-6 text-indigo-600" />
          Bảng điều khiển Lễ tân
        </h1>
        <p class="text-sm text-slate-400 mt-0.5">Giám sát hàng chờ trực tiếp, phân bổ bác sĩ và điều phối hoạt động tại sảnh phòng khám.</p>
      </div>
      
      <div class="flex flex-wrap items-center gap-3">
        <!-- Sync button -->
        <button @click="fetchDashboardData" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 gap-1.5">
          <RefreshCw class="w-4 h-4" />
          Đồng bộ hàng chờ
        </button>
        <!-- Walkin checkin button -->
        <a href="/receptionist/walk-ins" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-600/10 transition duration-200 hover:bg-indigo-700 hover:shadow-indigo-600/20 gap-1.5">
          <Plus class="w-4 h-4" />
          Tiếp nhận khám vãng lai
        </a>
        <!-- Today list button -->
        <a href="/receptionist/appointments" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50 gap-1.5">
          <Calendar class="w-4 h-4" />
          Lịch hẹn hôm nay
        </a>
      </div>
    </header>

    <!-- Quick Stats Cards with Premium Glows -->
    <section class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
      <!-- Stat 1: Doctors -->
      <article class="group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.04)] hover:-translate-y-0.5 relative overflow-hidden">
        <div class="card-glow bg-emerald-500/5"></div>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Bác sĩ sẵn sàng</p>
            <p class="mt-3 text-4xl font-extrabold text-slate-800 transition-colors group-hover:text-emerald-500">{{ doctors.length }}</p>
          </div>
          <div class="rounded-2xl bg-emerald-50 p-4 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
            <Stethoscope class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-3 text-xs text-slate-400">Đội ngũ y bác sĩ đang trong ca trực hiện tại.</p>
      </article>

      <!-- Stat 2: Live Queue -->
      <article class="group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.04)] hover:-translate-y-0.5 relative overflow-hidden">
        <div class="card-glow bg-indigo-500/5"></div>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Đang chờ hôm nay</p>
            <p class="mt-3 text-4xl font-extrabold text-slate-800 transition-colors group-hover:text-indigo-500">{{ queue.length }}</p>
          </div>
          <div class="rounded-2xl bg-indigo-50 p-4 text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
            <Clock class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-3 text-xs text-slate-400">Số lượng thú cưng đang xếp hàng chờ khám.</p>
      </article>

      <!-- Stat 3: Unpaid Invoice count -->
      <article class="group rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] transition-all duration-300 hover:shadow-[0_20px_40px_rgba(0,0,0,0.04)] hover:-translate-y-0.5 relative overflow-hidden">
        <div class="card-glow bg-rose-500/5"></div>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Hóa đơn chờ xử lý</p>
            <p class="mt-3 text-4xl font-extrabold text-rose-600 transition-colors group-hover:text-rose-700">{{ unpaidCount }}</p>
          </div>
          <div class="rounded-2xl bg-rose-50 p-4 text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 relative">
            <span v-if="unpaidCount > 0" class="absolute top-1 right-1 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
            </span>
            <CreditCard class="w-6 h-6" />
          </div>
        </div>
        <p class="mt-3 text-xs text-slate-400">Các ca khám đã xong đang chờ thanh toán dịch vụ.</p>
      </article>
    </section>

    <!-- Queue & Doctor workload grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
      <!-- Live Queue Section (Takes 2/3 columns) -->
      <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.01)] col-span-2">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
              Hàng chờ trực tiếp thời gian thực
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Thứ tự các ca khám bệnh đang chờ phân bổ hoặc đang xử lý tại phòng khám.</p>
          </div>
          <span class="text-xs font-bold px-2.5 py-1 bg-slate-50 text-slate-500 rounded-full border border-slate-100">Live Monitor</span>
        </div>

        <div class="flex flex-col gap-4 max-h-[500px] overflow-y-auto pr-1">
          <div v-if="queue.length === 0" class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/50">
            <Inbox class="w-12 h-12 text-slate-300 mb-3" />
            <p class="text-sm font-bold text-slate-500">Hàng chờ hiện đang trống.</p>
            <p class="text-xs text-slate-400 mt-1">Không có thú cưng nào đang xếp hàng chờ khám lúc này.</p>
          </div>
          
          <div v-for="app in queue" :key="app.id" :class="[
            'queue-item flex flex-col sm:flex-row sm:items-center justify-between rounded-2xl border p-4 shadow-sm transition-all duration-300', 
            app.is_emergency 
              ? 'border-rose-200 bg-rose-50/40 hover:bg-rose-50/70 shadow-rose-100/10 emergency-pulse' 
              : 'border-slate-100 bg-slate-50/20 hover:border-slate-200 hover:bg-slate-50/50'
          ]">
            <div class="flex items-start gap-4">
              <!-- Queue number badge -->
              <div :class="[
                'flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl text-base font-extrabold shadow-sm', 
                app.is_emergency ? 'bg-rose-600 text-white' : 'bg-indigo-600 text-white'
              ]">
                #{{ app.queue_number }}
              </div>
              
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <h3 class="font-extrabold text-slate-700 text-base">
                    {{ app.pet?.name || 'Thú cưng chưa rõ' }}
                  </h3>
                  <span v-if="app.is_emergency" class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[9px] font-extrabold text-rose-800 border border-rose-200 animate-pulse gap-1">
                    <AlertTriangle class="w-3 h-3" /> CẤP CỨU KHẨN CẤP
                  </span>
                </div>
                
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-xs text-slate-400">
                  <p class="flex items-center gap-1.5">
                    <span class="font-semibold text-slate-500">Chủ nuôi:</span> {{ app.owner?.name }}
                  </p>
                  <p class="flex items-center gap-1.5">
                    <span class="font-semibold text-slate-500">Bác sĩ chỉ định:</span> {{ app.doctor?.user?.name || 'Đang chờ phân bổ' }}
                  </p>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-2 mt-4 sm:mt-0 justify-end">
              <button v-if="!app.is_emergency" @click="markEmergency(app.id)" :disabled="isAlerting === app.id" class="inline-flex items-center rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100 hover:text-amber-800 transition duration-200 disabled:opacity-50 gap-1">
                <AlertTriangle class="w-3.5 h-3.5" /> Báo Cấp Cứu
              </button>
              <a :href="`/receptionist/appointments/${app.id}`" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 hover:border-indigo-500 hover:text-indigo-600 transition duration-200 shadow-sm">
                Chi tiết
              </a>
            </div>
          </div>
        </div>
      </article>

      <!-- Available Doctors Section (Takes 1/3 column) -->
      <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.01)] flex flex-col">
        <div class="mb-6">
          <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Bác sĩ ca trực
          </h2>
          <p class="text-xs text-slate-400 mt-0.5">Khối lượng công việc và số lượng ca khám đang chờ của từng bác sĩ.</p>
        </div>

        <div class="flex flex-col gap-4 max-h-[500px] overflow-y-auto pr-1">
          <div v-if="doctors.length === 0" class="flex flex-col items-center justify-center py-12 text-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/20">
            <Stethoscope class="w-8 h-8 text-slate-300 mb-2" />
            <p class="text-xs font-bold text-slate-500">Không có bác sĩ trong ca trực.</p>
          </div>
          
          <div v-for="doc in doctors" :key="doc.id" class="doc-card flex items-center justify-between rounded-2xl border border-slate-50 bg-[#FCFDFE] p-4 hover:border-slate-200 transition-all duration-300">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg">
                <Stethoscope class="w-5 h-5 text-indigo-500" />
              </div>
              <div>
                <p class="text-sm font-bold text-slate-700">BS. {{ doc.user?.name }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Khoa chuyên môn thú y</p>
              </div>
            </div>
            
            <span :class="getWorkloadClass(doc.pending_appointments_count || 0)" class="rounded-full px-2.5 py-1 text-[10px] font-bold border">
              Đang chờ: {{ doc.pending_appointments_count || 0 }}
            </span>
          </div>
        </div>
      </article>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Briefcase, RefreshCw, Plus, Calendar, Stethoscope, Clock, CreditCard, Inbox, AlertTriangle } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const queue = ref([]);
const doctors = ref([]);
const unpaidCount = ref(0);
const isAlerting = ref(null);

function getWorkloadClass(count) {
  if (count === 0) return 'bg-emerald-50 text-emerald-600 border-emerald-100';
  if (count <= 3) return 'bg-sky-50 text-sky-600 border-sky-100';
  return 'bg-rose-50 text-rose-600 border-rose-100';
}

async function fetchDashboardData() {
    isLoading.value = true;
    try {
        await Promise.all([
            fetchQueue(),
            fetchDoctors(),
            fetchUnpaidStats(),
        ]);
    } catch (e) {
        handleApiError(e);
    } finally {
        isLoading.value = false;
    }
}

async function fetchQueue() {
    const queueData = await callApi('/api/receptionist/queue', 'GET');
    queue.value = queueData?.data || [];
}

async function fetchDoctors() {
    const doctorsData = await callApi('/api/receptionist/doctors/available', 'GET');
    doctors.value = doctorsData?.data || [];
}

async function fetchUnpaidStats() {
    const unpaidData = await callApi('/api/receptionist/payments/unpaid', 'GET');
    const list = unpaidData?.data || [];
    unpaidCount.value = list.length;
}

async function markEmergency(appId) {
    if (!confirm('Bạn có chắc chắn muốn báo ca cấp cứu khẩn cấp cho thú cưng này không?')) return;
    
    isAlerting.value = appId;
    try {
        await callApi(`/api/receptionist/appointments/${appId}/emergency`, 'PATCH');
        notifySuccess('Đã kích hoạt cảnh báo cấp cứu khẩn cấp thành công!');
        await fetchQueue();
    } catch (e) {
        handleApiError(e);
    } finally {
        isAlerting.value = null;
    }
}

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
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
.group:hover .card-glow {
  transform: scale(1.3) rotate(-30deg);
}
.queue-item {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.queue-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.02);
}
.doc-card {
  transition: all 0.3s ease;
}
.doc-card:hover {
  transform: translateY(-1.5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}

@keyframes emergency-glow {
  0%, 100% { border-color: rgba(244, 63, 94, 0.3); box-shadow: 0 0 5px rgba(244, 63, 94, 0.1); }
  50% { border-color: rgba(244, 63, 94, 0.8); box-shadow: 0 0 15px rgba(244, 63, 94, 0.3); }
}

.emergency-pulse {
  animation: emergency-glow 2s infinite;
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
