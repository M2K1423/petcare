<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chi tiết lịch hẹn</h1>
            <p class="text-sm text-gray-500">Xem đầy đủ thông tin của lịch hẹn đã chọn.</p>
        </div>
        <a href="/receptionist/appointments" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Quay lại lịch hẹn
        </a>
    </div>

    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div v-if="error" class="rounded-xl bg-red-50 p-4 text-center text-red-600">{{ error }}</div>
        
        <div v-else-if="appointment" class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm text-gray-700">
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Pet:</span> {{ appointment.pet?.name || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Species:</span> {{ appointment.pet?.species?.name || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Owner:</span> {{ appointment.owner?.name || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Phone:</span> {{ appointment.owner?.phone || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Email:</span> {{ appointment.owner?.email || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Date time:</span> {{ formatDateTime(appointment.appointment_at) }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Status:</span> {{ appointment.status || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Queue number:</span> {{ appointment.queue_number ? String(appointment.queue_number) : 'Not in queue yet' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Doctor:</span> {{ appointment.doctor?.user?.name || 'Unassigned' }}</div>
            <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Service:</span> {{ appointment.service?.name || 'N/A' }}</div>
            <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2"><span class="font-semibold text-gray-900">Reason:</span> {{ appointment.reason || 'N/A' }}</div>
        </div>
    </article>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const isLoading = ref(true);
const appointment = ref(null);
const error = ref('');

function formatDateTime(dateTime) {
    if (!dateTime) return 'N/A';

    const date = new Date(dateTime);
    if (Number.isNaN(date.getTime())) return dateTime;

    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

onMounted(async () => {
    const root = document.querySelector('[data-page="receptionist-appointment-detail"]');
    if (root) {
        const id = Number(root.getAttribute('data-appointment-id'));
        if (!id) {
            error.value = 'Invalid appointment id.';
            isLoading.value = false;
            return;
        }

        try {
            const res = await callApi(`/api/receptionist/appointments/${id}`, 'GET');
            if (res && res.data) {
                appointment.value = res.data;
            } else {
                error.value = 'Appointment not found.';
            }
        } catch (e) {
            error.value = 'Failed to load appointment details.';
        }
    }
    isLoading.value = false;
});
</script>
