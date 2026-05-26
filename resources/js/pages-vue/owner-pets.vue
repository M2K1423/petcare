<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-5">
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Thêm thú cưng</h2>

            <form @submit.prevent="createPet" class="mt-4 space-y-3">
                <div>
                        <label for="pet-name" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Tên</label>
                    <input v-model="form.name" id="pet-name" name="name" required type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>

                <div>
                    <label for="pet-species" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Loài</label>
                    <select v-model="form.species_id" id="pet-species" name="species_id" required class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20">
                        <option v-if="speciesList.length === 0" value="">Đang tải...</option>
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
                    <textarea v-model="form.notes" id="pet-notes" name="notes" rows="2" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
                </div>

                <button type="submit" :disabled="isSaving" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35 disabled:opacity-50">
                    {{ isSaving ? 'Đang tạo...' : 'Tạo thú cưng' }}
                </button>
            </form>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Thú cưng của bạn</h2>
            <div class="mt-4 space-y-3">
                <p v-if="pets.length === 0" class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-4 text-sm text-[#4A4A4A]">No pets found. Create your first pet from the form.</p>
                
                <div v-for="pet in pets" :key="pet.id" class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-[#333333]">{{ pet.name }}</p>
                            <p class="mt-1 text-sm text-[#4A4A4A]">{{ formatPetMeta(pet) }}</p>
                            <p v-if="pet.notes" class="mt-2 text-sm text-[#4A4A4A]">Notes: {{ pet.notes }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a :href="`/owner/pets/${pet.id}/edit`" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Edit</a>
                            <a :href="`/owner/pets/${pet.id}/health-records`" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Health records</a>
                            <button @click="deletePet(pet.id)" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
    </div>
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
const pets = ref([]);
const speciesList = ref([]);

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

function toDateOnly(value) {
    if (!value) return value;
    const [datePart] = value.split('T');
    return datePart || value;
}

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

function translateGender(value) {
    const mapping = {
        male: 'Đực',
        female: 'Cái',
        unknown: 'Không rõ',
    };
    return mapping[value] || value;
}

function formatPetMeta(pet) {
    const chunks = [];
    if (pet.species?.name) chunks.push(`Loài: ${translateSpeciesName(pet.species.name)}`);
    chunks.push(`Giới tính: ${translateGender(pet.gender)}`);
    if (pet.weight) chunks.push(`Cân nặng: ${pet.weight} kg`);
    if (pet.birth_date) chunks.push(`Ngày sinh: ${toDateOnly(pet.birth_date)}`);
    return chunks.join(' | ');
}

async function loadData() {
    isLoading.value = true;
    try {
        const [speciesRes, petsRes] = await Promise.all([
            callApi('/api/owner/species', 'GET'),
            callApi('/api/owner/pets', 'GET')
        ]);
        
        speciesList.value = speciesRes.data || [];
        pets.value = petsRes.data || [];
        
        if (speciesList.value.length > 0 && !form.species_id) {
            form.species_id = speciesList.value[0].id;
        }
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
}

async function createPet() {
    isSaving.value = true;
    try {
        const payload = { ...form };
        if (payload.weight) payload.weight = Number(payload.weight);
        else payload.weight = null;
        
        await callApi('/api/owner/pets', 'POST', payload);
        
        // Reset form
        form.name = '';
        form.breed = '';
        form.birth_date = '';
        form.weight = '';
        form.color = '';
        form.allergies = '';
        form.notes = '';
        form.gender = 'unknown';
        if (speciesList.value.length > 0) form.species_id = speciesList.value[0].id;
        
        notifySuccess('Tạo thú cưng thành công.');
        await loadData();
    } catch (error) {
        handleApiError(error);
    } finally {
        isSaving.value = false;
    }
}

async function deletePet(id) {
    if (!window.confirm('Delete this pet?')) return;
    try {
        await callApi(`/api/owner/pets/${id}`, 'DELETE');
        notifySuccess('Pet deleted successfully.');
        await loadData();
    } catch (error) {
        handleApiError(error);
    }
}

onMounted(() => {
    loadData();
});
</script>
