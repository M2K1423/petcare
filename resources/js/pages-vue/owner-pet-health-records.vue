<template>
  <template v-if="!isLoading">
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#333333] flex items-center gap-2">
                        <Activity class="w-5 h-5 text-indigo-500" />
                        Bệnh án & lịch tiêm phòng
                    </h1>
                    <p class="mt-2 text-sm text-[#4A4A4A]">Theo dõi lịch sử khám và các mũi tiêm của thú cưng.</p>
                    <p class="mt-3 text-sm font-semibold text-[#2A6496]">Thú cưng: {{ petNameDisplay }}</p>
                </div>
                <a href="/owner/pets" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">
                    <ArrowLeft class="w-4 h-4 mr-1.5" />
                    Quay lại danh sách thú cưng
                </a>
            </div>

            <p class="mt-4 text-sm text-[#4A4A4A]">{{ statusMessage }}</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333] flex items-center gap-2">
                <FileText class="w-4 h-4 text-indigo-500" />
                Bệnh án
            </h2>
            <div class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
                <p v-if="medicalRecords.length === 0" class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-3">Chưa có bệnh án cho thú cưng này.</p>
                
                <article v-for="record in medicalRecords" :key="record.id" class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4">
                    <p class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">Ngày ghi nhận: {{ formatDate(record.record_date) }}</p>
                    <p class="mt-2"><span class="font-semibold text-[#333333]">Triệu chứng:</span> {{ record.symptoms || '-' }}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Chẩn đoán:</span> {{ record.diagnosis || '-' }}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Điều trị:</span> {{ record.treatment || '-' }}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Ghi chú:</span> {{ record.notes || '-' }}</p>
                </article>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333] flex items-center gap-2">
                <FileText class="w-4 h-4 text-emerald-500" />
                Lịch tiêm phòng
            </h2>
            <div class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
                <p v-if="vaccinations.length === 0" class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-3">Chưa có lịch tiêm phòng.</p>
                
                <article v-for="item in vaccinations" :key="item.id" class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4">
                    <p class="font-semibold text-[#333333]">{{ item.vaccine_name }}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Tiêm ngày: {{ formatDate(item.vaccinated_on) }}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Mũi tiếp theo: {{ item.next_due_on ? formatDate(item.next_due_on) : '-' }}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Số lô: {{ item.batch_number || '-' }}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Ghi chú: {{ item.notes || '-' }}</p>
                </article>
            </div>
        </article>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { ArrowLeft, Activity, FileText } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const isLoading = ref(true);
const statusMessage = ref('Đang tải...');
const pet = ref(null);
const medicalRecords = ref([]);
const vaccinations = ref([]);

const petNameDisplay = computed(() => {
    if (!pet.value) return 'Đang tải...';
    const speciesText = pet.value.species?.name ? ` (${pet.value.species.name})` : '';
    const breedText = pet.value.breed ? ` - ${pet.value.breed}` : '';
    return `${pet.value.name}${speciesText}${breedText}`;
});

function formatDate(input) {
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleDateString();
}

async function loadData() {
    const mountNode = document.querySelector('[data-page="owner-pet-health-records"]');
    const petId = Number(mountNode?.dataset?.petId);

    if (!petId) {
        statusMessage.value = 'Invalid pet id.';
        isLoading.value = false;
        return;
    }

    try {
        const response = await callApi(`/api/owner/pets/${petId}/health-records`, 'GET');
        pet.value = response.data.pet;
        medicalRecords.value = response.data.medical_records || [];
        vaccinations.value = response.data.vaccinations || [];
        statusMessage.value = 'Đã tải hồ sơ bệnh án và lịch tiêm phòng.';
    } catch (error) {
        statusMessage.value = error.message;
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    loadData();
});
</script>
