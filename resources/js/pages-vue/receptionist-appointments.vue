<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lịch hẹn hôm nay</h1>
            <p class="text-sm text-gray-500">Xem lại và xác nhận check-in lịch hẹn trong ngày.</p>
        </div>
        <a href="/receptionist/walkins" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Khách vãng lai mới
        </a>
    </div>

    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-gray-900">Lịch hẹn</h2>
            <div class="flex flex-wrap items-center gap-2">
                <input v-model="selectedDate" type="date" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-500">
                <button @click="loadAppointments" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Xem</button>
                <button @click="setToday" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hôm nay</button>
                <button @click="loadAppointments" class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-sync-alt"></i> Làm mới</button>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <div v-if="appointments.length === 0" class="rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500">
                Không tìm thấy lịch hẹn nào cho ngày {{ selectedDate }}.
            </div>
            
            <div v-for="app in appointments" :key="app.id" class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div>
                    <h3 class="font-bold text-gray-900">{{ app.pet?.name || 'Unknown Pet' }}</h3>
                    <p class="text-xs text-gray-500">Owner: {{ app.owner?.name }} ({{ app.owner?.phone || 'N/A' }})</p>
                    <p class="text-xs text-gray-500">Status: <span class="font-medium px-2 py-0.5 rounded-full bg-gray-100">{{ app.status }}</span></p>
                </div>
                <div class="flex flex-col gap-2">
                    <a :href="`/receptionist/appointments/${app.id}`" class="text-center rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Details</a>
                    <button v-if="canCheckIn(app)" @click="checkIn(app.id)" :disabled="isCheckingIn === app.id" class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200 disabled:opacity-50">
                        Check-in
                    </button>
                </div>
            </div>
        </div>
    </article>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const appointments = ref([]);
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const isCheckingIn = ref(null);

function canCheckIn(app) {
    return app.status === 'pending' || (app.status === 'confirmed' && !app.queue_number);
}

function setToday() {
    selectedDate.value = new Date().toISOString().split('T')[0];
    loadAppointments();
}

async function loadAppointments() {
    try {
        const res = await callApi(`/api/receptionist/appointments?date=${selectedDate.value}`, 'GET');
        appointments.value = res?.data || res?.data?.data || [];
    } catch (e) {
        handleApiError(e);
        appointments.value = [];
    }
}

async function checkIn(appId) {
    isCheckingIn.value = appId;
    try {
        await callApi(`/api/receptionist/appointments/${appId}/check-in`, 'PATCH');
        notifySuccess('Check-in successful!');
        await loadAppointments();
    } catch (e) {
        handleApiError(e);
    } finally {
        isCheckingIn.value = null;
    }
}

watch(selectedDate, () => {
    loadAppointments();
});

onMounted(() => {
    loadAppointments().finally(() => {
        isLoading.value = false;
    });
});
</script>
