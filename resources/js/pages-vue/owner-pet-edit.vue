<template>
  <template v-if="!isLoading">
    <header class="mb-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Không gian chủ nuôi</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[#333333] md:text-3xl">Chỉnh sửa thú cưng</h1>
                <p class="mt-1 text-sm text-[#4A4A4A]">Cập nhật thông tin chi tiết cho thú cưng của bạn.</p>
            </div>
            <a href="/owner/pets" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Quay lại danh sách</a>
        </div>
        <p class="mt-3 text-sm text-[#4A4A4A]">{{ statusMessage }}</p>
    </header>

    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur">
        <form @submit.prevent="updatePet" class="space-y-3">
            <div>
                <label for="pet-name" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Tên</label>
                <input v-model="form.name" id="pet-name" name="name" required type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="pet-species" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Loài</label>
                <select v-model="form.species_id" id="pet-species" name="species_id" required class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20">
                    <option v-for="s in speciesList" :key="s.id" :value="s.id">{{ translateSpeciesName(s.name) }}</option>
                </select>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="pet-gender" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giới tính</label>
                    <select v-model="form.gender" id="pet-gender" name="gender" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20">
                        <option value="unknown">Không rõ</option>
                        <option value="male">Đực</option>
                        <option value="female">Cái</option>
                    </select>
                </div>
                <div>
                    <label for="pet-weight" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Cân nặng (kg)</label>
                    <input v-model="form.weight" id="pet-weight" name="weight" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="pet-breed" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giống</label>
                    <input v-model="form.breed" id="pet-breed" name="breed" type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>
                <div>
                    <label for="pet-birth-date" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ngày sinh</label>
                    <input v-model="form.birth_date" id="pet-birth-date" name="birth_date" type="date" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>
            </div>

            <div>
                <label for="pet-color" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Màu sắc</label>
                <input v-model="form.color" id="pet-color" name="color" type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="pet-allergies" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Dị ứng</label>
                <textarea v-model="form.allergies" id="pet-allergies" name="allergies" rows="2" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
            </div>

            <div>
                <label for="pet-notes" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ghi chú</label>
                <textarea v-model="form.notes" id="pet-notes" name="notes" rows="3" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
            </div>

            <button type="submit" :disabled="isSaving" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35 disabled:opacity-50">
                {{ isSaving ? 'Đang lưu...' : 'Lưu thay đổi' }}
            </button>
        </form>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const isSaving = ref(false);
const statusMessage = ref('Đang tải...');
const speciesList = ref([]);
const petId = ref(null);

const form = reactive({
    name: '',
    species_id: '',
    gender: 'unknown',
    breed: '',
    birth_date: '',
    weight: '',
    color: '',
    allergies: '',
    notes: ''
});

function translateSpeciesName(name) {
    const normalized = name.trim().toLowerCase();
    const mapping = {
        bird: 'Chim',
        cat: 'Mèo',
        dog: 'Chó',
        fish: 'Cá',
        rabbit: 'Thỏ',
        hamster: 'Chuột hamster',
    };
    return mapping[normalized] || name;
}

async function loadData() {
    const mountNode = document.querySelector('[data-page="owner-pet-edit"]');
    petId.value = Number(mountNode?.dataset?.petId);
    
    if (!petId.value) {
        statusMessage.value = 'Mã thú cưng không hợp lệ.';
        return;
    }

    try {
        const [speciesRes, petRes] = await Promise.all([
            callApi('/api/owner/species', 'GET'),
            callApi(`/api/owner/pets/${petId.value}`, 'GET')
        ]);
        
        speciesList.value = speciesRes.data || [];
        const pet = petRes.data;
        
        form.name = pet.name ?? '';
        form.species_id = pet.species_id ?? '';
        form.gender = pet.gender ?? 'unknown';
        form.breed = pet.breed ?? '';
        form.birth_date = pet.birth_date ?? '';
        form.weight = pet.weight ?? '';
        form.color = pet.color ?? '';
        form.allergies = pet.allergies ?? '';
        form.notes = pet.notes ?? '';

        statusMessage.value = `Đang chỉnh sửa: ${pet.name}`;
    } catch (error) {
        handleApiError(error);
        statusMessage.value = 'Lỗi khi tải dữ liệu.';
    } finally {
        isLoading.value = false;
    }
}

async function updatePet() {
    isSaving.value = true;
    try {
        const payload = { ...form };
        if (payload.weight) payload.weight = Number(payload.weight);
        else payload.weight = null;
        
        await callApi(`/api/owner/pets/${petId.value}`, 'PUT', payload);
        notifySuccess('Lưu thành công!');
        statusMessage.value = `Đã lưu thành công: ${form.name}`;
    } catch (error) {
        handleApiError(error);
    } finally {
        isSaving.value = false;
    }
}

onMounted(() => {
    loadData();
});
</script>
