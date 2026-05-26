<template>
  <template v-if="!isLoading">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bảng điều khiển lễ tân</h1>
            <p class="text-sm text-gray-500">Quản lý hàng chờ, check-in và khách vãng lai.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="fetchDashboardData" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                <i class="fas fa-sync-alt mr-2"></i> Đồng bộ hàng chờ
            </button>
            <a href="/receptionist/walkins" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                + Thêm khách vãng lai
            </a>
            <a href="/receptionist/appointments" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Lịch hẹn hôm nay
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">Bác sĩ sẵn sàng</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ doctors.length }}</span>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">Đang chờ hôm nay</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ queue.length }}</span>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">Thanh toán chờ xử lý</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-red-600">{{ unpaidCount }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Live Queue -->
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm col-span-2">
            <h2 class="text-lg font-bold text-gray-900">Hàng chờ trực tiếp</h2>
            <div class="mt-4 flex flex-col gap-3">
                <div v-if="queue.length === 0" class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Hàng chờ hiện đang trống.</div>
                
                <div v-for="app in queue" :key="app.id" :class="['flex items-center justify-between rounded-xl border p-4 shadow-sm', app.is_emergency ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white']">
                    <div class="flex items-center gap-4">
                        <div :class="['flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full text-xl font-bold', app.is_emergency ? 'bg-red-200 text-red-700' : 'bg-blue-100 text-blue-700']">
                            #{{ app.queue_number }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">
                                {{ app.pet?.name || 'Unknown Pet' }}
                                <span v-if="app.is_emergency" class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">EMERGENCY</span>
                            </h3>
                            <p class="text-xs text-gray-500">Owner: {{ app.owner?.name }}</p>
                            <p class="text-xs text-gray-500">Doctor: {{ app.doctor?.user?.name || 'Pending Assignment' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button v-if="!app.is_emergency" @click="markEmergency(app.id)" :disabled="isAlerting === app.id" class="rounded-lg bg-orange-100 px-3 py-1.5 text-xs font-semibold text-orange-700 hover:bg-orange-200 disabled:opacity-50">Alert Emergency</button>
                    </div>
                </div>
            </div>
        </article>

        <!-- Available Doctors -->
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">Khối lượng bác sĩ</h2>
            <div class="mt-4 flex flex-col gap-3">
                <div v-if="doctors.length === 0" class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Không có bác sĩ trong ca trực.</div>
                
                <div v-for="doc in doctors" :key="doc.id" class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                    <span class="font-semibold text-gray-800">BS. {{ doc.user?.name }}</span>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Đang chờ: {{ doc.pending_appointments_count || 0 }}</span>
                </div>
            </div>
        </article>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const queue = ref([]);
const doctors = ref([]);
const unpaidCount = ref(0);
const isAlerting = ref(null);

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
    if (!confirm('Are you sure you want to flag this as an emergency?')) return;
    
    isAlerting.value = appId;
    try {
        await callApi(`/api/receptionist/appointments/${appId}/emergency`, 'PATCH');
        notifySuccess('Emergency alert triggered');
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
