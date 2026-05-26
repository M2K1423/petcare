<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Lịch theo tuần</h1>
            <p class="text-sm text-[#4A4A4A]">Xem các lịch hẹn được phân công trong bố cục theo tuần.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button @click="previousWeek" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">
                Tuần trước
            </button>
            <button @click="currentWeek" type="button" class="rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780]">
                Tuần này
            </button>
            <button @click="nextWeek" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">
                Tuần sau
            </button>
        </div>
    </div>

    <section class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Khoảng tuần</p>
            <p class="mt-1 text-sm font-bold text-[#2A6496]">{{ dateRange }}</p>
        </div>
        <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#B45309]">Tổng lịch hẹn</p>
            <p class="mt-1 text-2xl font-extrabold text-[#92400E]">{{ totalAppointments }}</p>
        </div>
        <div class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#027A48]">Hoàn thành</p>
            <p class="mt-1 text-2xl font-extrabold text-[#027A48]">{{ completedAppointments }}</p>
        </div>
    </section>

    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-4 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-7">
            <section v-for="day in weekDays" :key="day.dateStr" class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-3">
                <header class="mb-3 rounded-xl bg-[#EAF2FB] px-3 py-2 text-xs font-bold uppercase tracking-[0.08em] text-[#2A6496]">{{ day.label }}</header>
                <div class="space-y-2">
                    <p v-if="day.appointments.length === 0" class="rounded-xl border border-dashed border-[#C7CFDA] bg-white px-3 py-3 text-xs text-[#64748B]">Không có lịch hẹn</p>
                    
                    <article v-for="appointment in day.appointments" :key="appointment.id" class="rounded-xl border border-[#DDE1E6] bg-white p-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-[#333333]">{{ appointment.pet?.name ?? 'Thú cưng chưa rõ' }}</p>
                            <span :class="['rounded-full px-2 py-1 text-[10px] font-semibold', statusBadge(appointment.status)]">{{ formatStatusLabel(appointment.status) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-[#4A4A4A]">{{ appointment.pet?.species?.name ?? 'Chưa có' }} • {{ appointment.owner?.name ?? 'Chưa có' }}</p>
                        <p class="mt-1 text-xs text-[#4A4A4A]">{{ formatDateTime(appointment.appointment_at) }}</p>
                        <p class="mt-1 text-xs text-[#64748B]">Số thứ tự: {{ appointment.queue_number ?? 'Chưa có' }}</p>
                        <a :href="`/vet/appointments/${appointment.id}`" class="mt-2 inline-flex items-center rounded-lg border border-[#2A6496] bg-[#2A6496] px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-[#235780]">Mở ca</a>
                    </article>
                </div>
            </section>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { handleApiError } = useNotification();

const isLoading = ref(true);
const allAppointments = ref([]);
const weekAnchor = ref(new Date());

function toLocalDateString(value) {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function startOfWeek(date) {
    const copy = new Date(date);
    copy.setHours(0, 0, 0, 0);
    const day = copy.getDay();
    const offset = day === 0 ? -6 : 1 - day;
    copy.setDate(copy.getDate() + offset);
    return copy;
}

function addDays(date, days) {
    const copy = new Date(date);
    copy.setDate(copy.getDate() + days);
    return copy;
}

function formatDateLabel(date) {
    return date.toLocaleDateString('vi-VN', {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
    });
}

function formatStatusLabel(status) {
    const map = {
        awaiting_exam: 'Chờ khám',
        examining: 'Đang khám',
        awaiting_lab: 'Chờ xét nghiệm',
        treating: 'Đang điều trị',
        completed: 'Hoàn thành',
        confirmed: 'Đã xác nhận',
        cancelled: 'Đã hủy',
    };
    return map[status] ?? status;
}

function formatDateTime(input) {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;

    return date.toLocaleString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
    });
}

function statusBadge(status) {
    if (status === 'completed') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

const weekDays = computed(() => {
    const weekStart = startOfWeek(weekAnchor.value);
    const dates = Array.from({ length: 7 }, (_, i) => addDays(weekStart, i));
    
    return dates.map(date => {
        const dateStr = toLocalDateString(date);
        const dayAppointments = allAppointments.value
            .filter(app => app.appointment_at?.slice(0, 10) === dateStr)
            .sort((a, b) => a.appointment_at.localeCompare(b.appointment_at));
            
        return {
            dateStr,
            label: formatDateLabel(date),
            appointments: dayAppointments
        };
    });
});

const dateRange = computed(() => {
    const weekStart = startOfWeek(weekAnchor.value);
    const weekEnd = addDays(weekStart, 6);
    return `${toLocalDateString(weekStart)} - ${toLocalDateString(weekEnd)}`;
});

const totalAppointments = computed(() => {
    return weekDays.value.reduce((sum, day) => sum + day.appointments.length, 0);
});

const completedAppointments = computed(() => {
    return weekDays.value.reduce((sum, day) => {
        return sum + day.appointments.filter(app => app.status === 'completed').length;
    }, 0);
});

function previousWeek() {
    weekAnchor.value = addDays(weekAnchor.value, -7);
}

function currentWeek() {
    weekAnchor.value = new Date();
}

function nextWeek() {
    weekAnchor.value = addDays(weekAnchor.value, 7);
}

async function loadAppointments() {
    isLoading.value = true;
    try {
        const response = await callApi('/api/vet/appointments', 'GET');
        allAppointments.value = response.data || [];
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    loadAppointments();
});
</script>
