<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-6">
      <!-- Section header with gradient line -->
      <div class="border-b border-slate-100 pb-4">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 flex items-center gap-2">
          <PawPrint class="w-6 h-6 text-indigo-500" />
          Quản lý Thú cưng
        </h1>
        <p class="text-sm text-slate-400 mt-1">Thêm mới, chỉnh sửa thông tin và theo dõi hồ sơ y tế các bé cưng của bạn.</p>
      </div>

      <section class="grid gap-8 lg:grid-cols-5">
        <!-- Add Pet Form Panel (2/5 columns) -->
        <article class="lg:col-span-2 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur relative overflow-hidden">
          <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            Thêm thú cưng mới
          </h2>

          <form @submit.prevent="createPet" class="space-y-4">
            <div>
              <label for="pet-name" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Tên bé cưng</label>
              <input v-model="form.name" id="pet-name" name="name" required type="text" placeholder="Nhập tên bé..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
            </div>

            <div>
              <label for="pet-species" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Loài</label>
              <select v-model="form.species_id" id="pet-species" name="species_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                <option v-if="speciesList.length === 0" value="">Đang tải...</option>
                <option v-for="s in speciesList" :key="s.id" :value="s.id">{{ translateSpeciesName(s.name) }}</option>
              </select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="pet-gender" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Giới tính</label>
                <select v-model="form.gender" id="pet-gender" name="gender" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                  <option value="unknown">Không rõ</option>
                  <option value="male">Đực</option>
                  <option value="female">Cái</option>
                </select>
              </div>
              <div>
                <label for="pet-weight" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Cân nặng (kg)</label>
                <input v-model="form.weight" id="pet-weight" name="weight" type="number" min="0" step="0.01" placeholder="Cân nặng..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="pet-breed" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Giống loại</label>
                <input v-model="form.breed" id="pet-breed" name="breed" type="text" placeholder="Ví dụ: Poodle, Corgi..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
              </div>
              <div>
                <label for="pet-birth-date" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Ngày sinh</label>
                <input v-model="form.birth_date" id="pet-birth-date" name="birth_date" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
              </div>
            </div>

            <div>
              <label for="pet-color" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Màu sắc</label>
              <input v-model="form.color" id="pet-color" name="color" type="text" placeholder="Màu lông..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
            </div>

            <div>
              <label for="pet-allergies" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Dị ứng (nếu có)</label>
              <textarea v-model="form.allergies" id="pet-allergies" name="allergies" rows="2" placeholder="Ghi chú về dị ứng thức ăn, thuốc..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></textarea>
            </div>

            <div>
              <label for="pet-notes" class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Ghi chú sức khỏe</label>
              <textarea v-model="form.notes" id="pet-notes" name="notes" rows="2" placeholder="Sở thích, lưu ý chăm sóc đặc biệt..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"></textarea>
            </div>

            <button type="submit" :disabled="isSaving" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition-all duration-300 hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:opacity-50 gap-1.5">
              <Sparkles v-if="!isSaving" class="w-4 h-4" />
              {{ isSaving ? 'Đang tạo...' : 'Tạo hồ sơ thú cưng' }}
            </button>
          </form>
        </article>

        <!-- Pets List Panel (3/5 columns) -->
        <article class="lg:col-span-3 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              Danh sách bé cưng của bạn
            </h2>
            <span class="text-xs font-bold px-2.5 py-1 bg-slate-50 text-slate-500 rounded-full border border-slate-100">{{ filteredPets.length }} Bé</span>
          </div>

          <!-- MỚI: Thanh tìm kiếm nhanh thú cưng -->
          <div class="mb-4 relative">
            <input v-model="searchQuery" type="text" placeholder="Tìm nhanh theo tên bé cưng, giống loại, ghi chú..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-xs text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
              <Search class="w-4 h-4" />
            </span>
          </div>

          <div class="space-y-4 max-h-[750px] overflow-y-auto pr-1">
            <div v-if="filteredPets.length === 0" class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/50">
              <PawPrint class="w-12 h-12 text-slate-300 mb-3" />
              <p class="text-sm font-bold text-slate-500">Không tìm thấy bé cưng nào phù hợp.</p>
              <p class="text-xs text-slate-400 mt-1">Vui lòng kiểm tra lại từ khóa tìm kiếm của bạn.</p>
            </div>
            
            <div v-for="pet in filteredPets" :key="pet.id" class="pet-card group rounded-2xl border border-slate-100 bg-slate-50/20 p-5 shadow-sm hover:shadow-[0_12px_24px_rgba(0,0,0,0.03)] hover:border-slate-200 transition-all duration-300 relative overflow-hidden">
              <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/[0.02] rounded-bl-full pointer-events-none"></div>
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                  <!-- Custom Avatar depending on species -->
                  <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl group-hover:scale-105 transition-transform duration-300">
                    <PawPrint class="w-7 h-7" />
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <p class="text-base font-extrabold text-slate-700">{{ pet.name }}</p>
                      <span :class="getGenderClass(pet.gender)" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                        {{ translateGender(pet.gender) }}
                      </span>
                    </div>
                    
                    <div class="mt-2 grid gap-1 grid-cols-1 sm:grid-cols-2 text-xs text-slate-400">
                      <p class="flex items-center gap-1">
                        <span class="font-semibold text-slate-500">Loài:</span> {{ translateSpeciesName(pet.species?.name) }}
                      </p>
                      <p v-if="pet.breed" class="flex items-center gap-1">
                        <span class="font-semibold text-slate-500">Giống:</span> {{ pet.breed }}
                      </p>
                      <p v-if="pet.weight" class="flex items-center gap-1">
                        <span class="font-semibold text-slate-500">Cân nặng:</span> {{ pet.weight }} kg
                      </p>
                      <p v-if="pet.birth_date" class="flex items-center gap-1">
                        <span class="font-semibold text-slate-500">Ngày sinh:</span> {{ toDateOnly(pet.birth_date) }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="flex flex-wrap sm:flex-col justify-end gap-2.5 sm:self-center">
                  <a :href="`/owner/pets/${pet.id}/edit`" class="btn-action inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-indigo-500 hover:text-indigo-600 gap-1">
                    <Pencil class="w-3 h-3 text-slate-400 group-hover:text-indigo-500" />
                    Chỉnh sửa
                  </a>
                  <a :href="`/owner/pets/${pet.id}/health-records`" class="btn-action inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-amber-500 hover:text-amber-600 gap-1">
                    <FileText class="w-3 h-3 text-slate-400 group-hover:text-amber-500" />
                    Bệnh án
                  </a>
                  <button @click="deletePet(pet.id)" class="btn-action inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-rose-500 hover:text-rose-600 gap-1">
                    <Trash2 class="w-3 h-3 text-slate-400 group-hover:text-rose-500" />
                    Xóa hồ sơ
                  </button>
                </div>
              </div>

              <!-- Notes Callout -->
              <div v-if="pet.notes || pet.allergies" class="mt-4 pt-3 border-t border-slate-100 flex flex-col gap-1.5 text-xs text-slate-500">
                <p v-if="pet.allergies" class="flex items-start gap-1">
                  <AlertTriangle class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" />
                  <span class="font-bold text-rose-500">Dị ứng:</span> {{ pet.allergies }}
                </p>
                <p v-if="pet.notes" class="flex items-start gap-1">
                  <FileText class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" />
                  <span class="font-bold text-slate-600">Ghi chú:</span> {{ pet.notes }}
                </p>
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
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';
import { PawPrint, Sparkles, Search, Pencil, FileText, Trash2, AlertTriangle } from '@lucide/vue';

const { notifySuccess, handleApiError } = useNotification();

const isLoading = ref(true);
const isSaving = ref(false);
const pets = ref([]);
const speciesList = ref([]);
const searchQuery = ref('');

const filteredPets = computed(() => {
    if (!searchQuery.value) return pets.value;
    const q = searchQuery.value.toLowerCase().trim();
    return pets.value.filter(pet => {
        const petName = (pet.name || '').toLowerCase();
        const petBreed = (pet.breed || '').toLowerCase();
        const petNotes = (pet.notes || '').toLowerCase();
        const petSpecies = (pet.species?.name || '').toLowerCase();
        return petName.includes(q) || petBreed.includes(q) || petNotes.includes(q) || petSpecies.includes(q);
    });
});

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
    if (!name) return 'Chưa rõ';
    const normalized = name.trim().toLowerCase();
    const mapping = {
        bird: 'Chim',
        cat: 'Mèo',
        dog: 'Chó',
        fish: 'Cá',
        rabbit: 'Thỏ',
        hamster: 'Chuột Hamster',
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

function getGenderClass(value) {
  switch (value) {
    case 'male': return 'bg-sky-50 text-sky-600 border-sky-100';
    case 'female': return 'bg-rose-50 text-rose-600 border-rose-100';
    default: return 'bg-slate-50 text-slate-500 border-slate-100';
  }
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
        
        notifySuccess('Tạo hồ sơ thú cưng thành công!');
        await loadData();
    } catch (error) {
        handleApiError(error);
    } finally {
        isSaving.value = false;
    }
}

async function deletePet(id) {
    if (!window.confirm('Bạn có chắc chắn muốn xóa hồ sơ bé cưng này không?')) return;
    try {
        await callApi(`/api/owner/pets/${id}`, 'DELETE');
        notifySuccess('Đã xóa hồ sơ thú cưng thành công!');
        await loadData();
    } catch (error) {
        handleApiError(error);
    }
}

const onGlobalSearch = (e) => {
    searchQuery.value = e.detail;
};

onMounted(() => {
    loadData();
    window.addEventListener('petcare-global-search', onGlobalSearch);
    const globalSearchInput = document.getElementById('global-search-input');
    if (globalSearchInput) {
        searchQuery.value = globalSearchInput.value;
    }
});

onUnmounted(() => {
    window.removeEventListener('petcare-global-search', onGlobalSearch);
});
</script>

<style scoped>
.pet-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.pet-card:hover {
  transform: translateY(-2px);
}
.btn-action {
  transition: all 0.2s ease-in-out;
}
.btn-action:hover {
  transform: scale(1.02);
}
::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>
