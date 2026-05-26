<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Lịch hẹn bác sĩ</h1>
            <p class="text-sm text-[#4A4A4A]">Xem các ca được phân công và mở từng lịch hẹn để lưu bệnh án.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="vet-date-filter" class="text-sm font-semibold text-[#333333]">Ngày</label>
                <input v-model="filters.date" id="vet-date-filter" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
            </div>
            <div>
                <label for="vet-status-filter" class="text-sm font-semibold text-[#333333]">Trạng thái</label>
                <select v-model="filters.workflow_status" id="vet-status-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="">Tất cả trạng thái</option>
                    <option value="awaiting_exam">Chờ khám</option>
                    <option value="examining">Đang khám</option>
                    <option value="awaiting_lab">Chờ xét nghiệm</option>
                    <option value="treating">Đang điều trị</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="follow_up">Tái khám</option>
                </select>
            </div>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Hôm nay</p>
            <p class="mt-1 text-2xl font-extrabold text-[#2A6496]">{{ todayCount }}</p>
        </div>
        <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#B45309]">Chờ khám</p>
            <p class="mt-1 text-2xl font-extrabold text-[#92400E]">{{ confirmedCount }}</p>
        </div>
        <div class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#027A48]">Hoàn thành</p>
            <p class="mt-1 text-2xl font-extrabold text-[#027A48]">{{ completedCount }}</p>
        </div>
    </section>

    <section class="mt-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="space-y-4 text-sm text-[#4A4A4A]">
            <div v-if="appointments.length === 0" class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">Không có lịch hẹn phù hợp bộ lọc này.</div>
            
            <article v-for="appointment in appointments" :key="appointment.id" class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.03)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-bold text-[#333333]">{{ appointment.pet?.name ?? 'Thú cưng chưa rõ' }}{{ appointment.pet?.species?.name ? ` • ${appointment.pet.species.name}` : '' }}</p>
                        <p class="mt-1 text-xs text-[#4A4A4A]">Chủ nuôi: {{ appointment.owner?.name ?? 'Chưa có' }}{{ appointment.owner?.phone ? ` • ${appointment.owner.phone}` : '' }}</p>
                        <p class="mt-1 text-xs text-[#4A4A4A]">Thời gian: {{ formatDateTime(appointment.appointment_at) }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span :class="['inline-flex rounded-full px-3 py-1 text-xs font-semibold', statusTone(appointment.workflow_status ?? appointment.status)]">{{ formatWorkflowStatus(appointment.workflow_status) }}</span>
                        <span class="text-xs text-[#64748B]">{{ appointment.medical_record ? 'Đã lưu bệnh án' : 'Chờ bệnh án' }}</span>
                    </div>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-3 text-sm text-[#4A4A4A]">
                    <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Số thứ tự:</span> {{ appointment.queue_number ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Dịch vụ:</span> {{ appointment.service?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Lý do:</span> {{ appointment.reason ?? 'Chưa có' }}</div>
                </div>
                <div class="mt-4">
                    <button v-if="(appointment.workflow_status ?? 'awaiting_exam') === 'awaiting_exam'" @click="acceptAppointment(appointment.id)" class="mr-2 inline-flex items-center rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0C734F] disabled:opacity-50" :disabled="acceptingId === appointment.id">
                        {{ acceptingId === appointment.id ? 'Đang nhận...' : 'Nhận ca khám' }}
                    </button>
                    <a :href="`/vet/appointments/${appointment.id}`" class="inline-flex items-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#235780]">
                        Mở ca khám
                    </a>
                </div>
            </article>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const appointments = ref([]);
const acceptingId = ref(null);

const filters = reactive({
    date: '',
    workflow_status: ''
});

function toLocalDateString(value) {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

const todayCount = computed(() => {
    const today = toLocalDateString(new Date());
    return appointments.value.filter(item => item.appointment_at?.slice(0, 10) === today).length;
});

const confirmedCount = computed(() => {
    return appointments.value.filter(item => (item.workflow_status ?? 'awaiting_exam') === 'awaiting_exam').length;
});

const completedCount = computed(() => {
    return appointments.value.filter(item => item.status === 'completed').length;
});

function formatDateTime(input) {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatWorkflowStatus(status) {
    if (!status) return 'Chờ khám';
    const map = {
        awaiting_exam: 'Chờ khám',
        examining: 'Đang khám',
        awaiting_lab: 'Chờ xét nghiệm',
        treating: 'Đang điều trị',
        completed: 'Hoàn thành',
        follow_up: 'Tái khám',
    };
    return map[status] ?? status;
}

function statusTone(status) {
    if (status === 'completed') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'follow_up') return 'bg-[#EFF6FF] text-[#1D4ED8]';
    if (status === 'treating') return 'bg-[#EDE9FE] text-[#5B21B6]';
    if (status === 'awaiting_lab') return 'bg-[#FFF7ED] text-[#C2410C]';
    if (status === 'examining') return 'bg-[#FEF3C7] text-[#92400E]';
    if (status === 'awaiting_exam' || status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

async function acceptAppointment(id) {
    acceptingId.value = id;
    try {
        await callApi(`/api/vet/appointments/${id}/accept`, 'PATCH');
        notifySuccess('Đã nhận ca khám.');
        await loadAppointments();
    } catch (error) {
        handleApiError(error);
    } finally {
        acceptingId.value = null;
    }
}

async function loadAppointments() {
    isLoading.value = true;
    try {
        const params = new URLSearchParams();
        if (filters.date) params.set('date', filters.date);
        if (filters.workflow_status) params.set('workflow_status', filters.workflow_status);

        const url = params.size > 0 ? `/api/vet/appointments?${params.toString()}` : '/api/vet/appointments';
        const response = await callApi(url, 'GET');
        appointments.value = response.data || [];
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
}

watch(filters, () => {
    loadAppointments();
}, { deep: true });

onMounted(() => {
    loadAppointments();
});
</script>
