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
            <div class="rounded-xl bg-gray-50 p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:col-span-2">
                <div>
                    <span class="font-semibold text-gray-900">Bác sĩ khám:</span>
                    <span class="ml-1 text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200">
                        {{ appointment.doctor?.user?.name || 'Chưa phân công' }}
                    </span>
                </div>
                <div v-if="appointment.status !== 'completed' && appointment.status !== 'cancelled'" class="flex items-center gap-2">
                    <select v-model="selectedDoctorId" class="rounded-xl border border-gray-200 bg-white px-2 py-1.5 text-sm outline-none focus:border-blue-500">
                        <option value="">-- Chọn bác sĩ --</option>
                        <option v-for="doc in doctors" :key="doc.id" :value="doc.id">BS. {{ doc.user?.name }}</option>
                    </select>
                    <button @click="assignDoctor" :disabled="isAssigning" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition">
                        {{ isAssigning ? 'Đang lưu...' : 'Phân công' }}
                    </button>
                </div>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2"><span class="font-semibold text-gray-900">Service:</span> {{ appointment.service?.name || 'N/A' }}</div>
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
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const appointment = ref(null);
const error = ref('');
const doctors = ref([]);
const selectedDoctorId = ref('');
const isAssigning = ref(false);

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

async function assignDoctor() {
    if (!selectedDoctorId.value) {
        alert('Vui lòng chọn bác sĩ để phân công.');
        return;
    }
    isAssigning.value = true;
    try {
        const root = document.querySelector('[data-page="receptionist-appointment-detail"]');
        const id = Number(root.getAttribute('data-appointment-id'));
        
        const res = await callApi(`/api/receptionist/appointments/${id}/assign-doctor`, 'POST', {
            doctor_id: Number(selectedDoctorId.value)
        });
        
        if (res && res.data) {
            appointment.value = res.data;
            notifySuccess('Phân công bác sĩ thành công!');
        }
    } catch (e) {
        handleApiError(e);
    } finally {
        isAssigning.value = false;
    }
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
            const [res, doctorsRes] = await Promise.all([
                callApi(`/api/receptionist/appointments/${id}`, 'GET'),
                callApi('/api/receptionist/doctors/available', 'GET').catch(() => ({ data: [] }))
            ]);
            
            if (res && res.data) {
                appointment.value = res.data;
                selectedDoctorId.value = res.data.doctor_id || '';
            } else {
                error.value = 'Appointment not found.';
            }
            
            doctors.value = doctorsRes.data || [];
        } catch (e) {
            error.value = 'Failed to load appointment details.';
        }
    }
    isLoading.value = false;
});
</script>
