<template>
  <template v-if="!isLoading">
    <!-- Page Header with Back Lnk -->
    <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5">
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 mb-2">
          Lễ tân sảnh chờ
        </span>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">🚶‍♂️ Tiếp nhận Khách vãng lai</h1>
        <p class="text-sm text-slate-400 mt-0.5">Đăng ký nhanh hồ sơ khách hàng mới và xếp thú cưng trực tiếp vào hàng chờ khám đột xuất.</p>
      </div>
      <a href="/receptionist/dashboard" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50">
        ⬅️ Quay lại Bảng điều khiển
      </a>
    </header>

    <!-- Center Styled Interactive Card -->
    <article class="w-full max-w-2xl mx-auto rounded-3xl border border-slate-100 bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur relative overflow-hidden">
      <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/[0.01] rounded-bl-full pointer-events-none"></div>
      
      <form @submit.prevent="submitWalkIn" class="space-y-6">
        <!-- Section 1: Owner Information -->
        <fieldset class="space-y-3.5 border-b border-slate-100 pb-5">
          <legend class="text-sm font-extrabold text-slate-700 flex items-center gap-2 mb-2">
            <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
            Thông tin chủ nuôi (Owner)
          </legend>
          <input v-model="form.owner_name" type="text" placeholder="Họ và tên khách hàng..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required>
          
          <div class="grid gap-4 sm:grid-cols-2">
            <input v-model="form.owner_phone" type="tel" placeholder="Số điện thoại di động..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required>
            <input v-model="form.owner_email" type="email" placeholder="Địa chỉ email (Không bắt buộc)..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
          </div>
        </fieldset>

        <!-- Section 2: Pet Information -->
        <fieldset class="space-y-3.5 border-b border-slate-100 pb-5">
          <legend class="text-sm font-extrabold text-slate-700 flex items-center gap-2 mb-2">
            <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
            Thông tin bé thú cưng
          </legend>
          
          <div class="grid gap-4 sm:grid-cols-2">
            <input v-model="form.pet_name" type="text" placeholder="Tên bé cưng..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required>
            <input v-model="form.weight" type="number" step="0.1" placeholder="Cân nặng hiện tại (kg)..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
          </div>

          <select v-model="form.species_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required>
            <option value="" disabled>Chọn loài thú cưng...</option>
            <option v-for="sp in species" :key="sp.id" :value="sp.id">🐾 {{ translateSpeciesName(sp.name) }}</option>
          </select>
        </fieldset>

        <!-- Section 3: Doctor Assign -->
        <fieldset class="space-y-3.5 border-b border-slate-100 pb-5">
          <legend class="text-sm font-extrabold text-slate-700 flex items-center gap-2 mb-2">
            <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
            Phân công Bác sĩ điều trị
          </legend>
          <select v-model="form.doctor_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            <option value="">🍀 Tự động phân công ngẫu nhiên bác sĩ đang rảnh</option>
            <option v-for="doc in doctors" :key="doc.id" :value="doc.id">👨‍⚕️ BS. {{ doc.user?.name || `Bác sĩ #${doc.id}` }}</option>
          </select>
        </fieldset>

        <!-- Section 4: Symptoms -->
        <fieldset class="space-y-3.5">
          <legend class="text-sm font-extrabold text-slate-700 flex items-center gap-2 mb-2">
            <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
            Lý do khám & Tình trạng lâm sàng
          </legend>
          
          <select v-model="conditionOption" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            <option value="">🔎 Chọn nhóm triệu chứng phổ biến...</option>
            <option value="Khám tổng quát">🩺 Khám tổng quát định kỳ</option>
            <option value="Tiêm phòng">💉 Tiêm phòng vắc-xin</option>
            <option value="Vấn đề tiêu hóa">🤢 Biểu hiện tiêu hóa (Bỏ ăn, nôn mửa)</option>
            <option value="Vấn đề da liễu">🧼 Bệnh lý da liễu (Ngứa, rụng lông)</option>
            <option value="Chấn thương">🩹 Chấn thương ngoại khoa (Đi khập khiễng)</option>
            <option value="Other">📝 Lý do khác (Tự nhập chi tiết dưới đây)</option>
          </select>
          
          <input v-model="conditionCustom" :readonly="!isCustomCondition" type="text" :placeholder="isCustomCondition ? 'Nhập chi tiết bệnh lý hoặc lý do khám...' : 'Ghi chú thêm triệu chứng (Không bắt buộc)...'" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
        </fieldset>

        <!-- Section 5: Emergency Flag with Pulsating Warning -->
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-50/50 border border-rose-100 transition-all duration-300 hover:bg-rose-50">
          <input v-model="form.is_emergency" type="checkbox" id="is_emergency" class="h-5 w-5 rounded border-rose-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
          <label for="is_emergency" class="text-sm font-extrabold text-rose-600 cursor-pointer select-none flex items-center gap-1.5">
            🚨 ĐÁNH DẤU CA CẤP CỨU KHẨN CẤP (Xử lý ngay lập tức)
          </label>
        </div>

        <button type="submit" :disabled="isSubmitting" class="w-full rounded-xl bg-indigo-600 px-4 py-3.5 font-bold text-white shadow-lg shadow-indigo-600/10 hover:bg-indigo-700 hover:shadow-indigo-600/20 transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2">
          {{ isSubmitting ? 'Đang xếp hàng chờ...' : '✨ Đăng ký & Đưa vào hàng chờ trực tiếp' }}
        </button>
      </form>
    </article>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const isSubmitting = ref(false);
const species = ref([]);
const doctors = ref([]);

const form = reactive({
    owner_name: '',
    owner_phone: '',
    owner_email: '',
    pet_name: '',
    weight: '',
    species_id: '',
    doctor_id: '',
    is_emergency: false,
});

const conditionOption = ref('');
const conditionCustom = ref('');

const isCustomCondition = computed(() => {
    return conditionOption.value === 'Other' || conditionOption.value === '';
});

async function loadInitialData() {
    try {
        const [speciesRes, doctorsRes] = await Promise.all([
            callApi('/api/receptionist/species', 'GET').catch(() => ({ data: [] })),
            callApi('/api/receptionist/doctors/available', 'GET').catch(() => ({ data: [] }))
        ]);
        
        species.value = speciesRes.data || [];
        if (species.value.length === 0) {
            species.value = [
                { id: 1, name: 'Chó' },
                { id: 2, name: 'Mèo' },
                { id: 3, name: 'Chim' }
            ];
        }
        
        doctors.value = doctorsRes.data || [];
    } catch (e) {
        console.error(e);
    }
}

async function submitWalkIn() {
    isSubmitting.value = true;
    
    let reason = '';
    const selectedCondition = conditionOption.value.trim();
    const customCondition = conditionCustom.value.trim();

    if (selectedCondition && selectedCondition !== 'Other') {
        reason = customCondition ? `${selectedCondition} - ${customCondition}` : selectedCondition;
    } else if (customCondition) {
        reason = customCondition;
    }

    const payload = {
        owner_name: form.owner_name,
        owner_phone: form.owner_phone,
        owner_email: form.owner_email,
        pet_name: form.pet_name,
        weight: form.weight,
        species_id: form.species_id,
        doctor_id: form.doctor_id,
        is_emergency: form.is_emergency ? '1' : '0',
        reason: reason
    };

    try {
        await callApi('/api/receptionist/customers/walk-in', 'POST', payload);
        notifySuccess('Đăng ký khách vãng lai và đưa vào hàng chờ thành công!');
        
        // Reset form
        form.owner_name = '';
        form.owner_phone = '';
        form.owner_email = '';
        form.pet_name = '';
        form.weight = '';
        form.species_id = '';
        form.doctor_id = '';
        form.is_emergency = false;
        conditionOption.value = '';
        conditionCustom.value = '';
        
        // Redirect to dashboard to see the live queue monitor
        setTimeout(() => {
            window.location.href = '/receptionist/dashboard';
        }, 1500);
    } catch (e) {
        handleApiError(e);
    } finally {
        isSubmitting.value = false;
    }
}

function translateSpeciesName(name) {
    if (!name) return 'Chưa rõ';
    const normalized = name.trim().toLowerCase();
    const mapping = {
        bird: 'Chim',
        cat: 'Mèo',
        dog: 'Chó',
        fish: 'Cá cảnh',
        rabbit: 'Thỏ',
        hamster: 'Chuột Hamster',
        reptile: 'Bò sát',
        turtle: 'Rùa',
        hedgehog: 'Nhím cảnh'
    };
    return mapping[normalized] || name;
}

onMounted(() => {
    loadInitialData().finally(() => {
        isLoading.value = false;
    });
});
</script>

<style scoped>
/* tactile click feedback */
button:active {
  transform: scale(0.98);
}
</style>
