<template>
  <template v-if="!isLoading">
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Thú cưng của bạn</p>
            <p class="mt-3 text-4xl font-extrabold text-[#2A6496]">{{ pets.length }}</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Tổng số hồ sơ thú cưng trong tài khoản của bạn.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Lịch hẹn</p>
            <p class="mt-3 text-4xl font-extrabold text-[#2A6496]">{{ appointments.length }}</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Tổng số lịch hẹn bạn đã tạo.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Lịch hẹn chờ xử lý</p>
            <p class="mt-3 text-4xl font-extrabold text-[#2A6496]">{{ pendingCount }}</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Các lịch hẹn đang chờ xác nhận.</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Thao tác nhanh</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="/owner/pets" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">Thêm hoặc sửa thú cưng</a>
                <a href="/owner/appointments" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Đặt lịch hẹn</a>
                <a href="/owner/shop" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Mua thuốc</a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Lịch hẹn gần đây</h2>
            <div class="mt-4 space-y-2 text-sm text-[#4A4A4A]">
                <p v-if="appointments.length === 0">No appointments yet.</p>
                <div v-for="item in recentAppointments" :key="item.id" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2">
                    <p class="font-semibold text-[#333333]">{{ item.pet?.name ?? 'Unknown pet' }}</p>
                    <p class="text-xs text-[#4A4A4A]">{{ formatDateTime(item.appointment_at) }} | {{ item.status }}</p>
                </div>
            </div>
        </article>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const pets = ref([]);
const appointments = ref([]);
const isLoading = ref(true);

const pendingCount = computed(() => appointments.value.filter(a => a.status === 'pending').length);
const recentAppointments = computed(() => appointments.value.slice(0, 5));

function formatDateTime(input) {
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString();
}

async function bootstrap() {
    isLoading.value = true;
    try {
        const [petsResponse, appointmentsResponse] = await Promise.all([
            callApi('/api/owner/pets', 'GET'),
            callApi('/api/owner/appointments', 'GET'),
        ]);

        pets.value = petsResponse.data || [];
        appointments.value = appointmentsResponse.data || [];
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
