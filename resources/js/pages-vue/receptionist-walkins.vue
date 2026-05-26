<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Đăng ký khách vãng lai</h1>
            <p class="text-sm text-gray-500">Tạo hồ sơ khách vãng lai mới và đưa thú cưng vào hàng chờ.</p>
        </div>
        <a href="/receptionist/appointments" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Xem lịch hẹn hôm nay
        </a>
    </div>

    <article class="w-full max-w-2xl mx-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form @submit.prevent="submitWalkIn" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Thông tin chủ nuôi</label>
                <input v-model="form.owner_name" type="text" placeholder="Tên khách hàng" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]" required>
                <input v-model="form.owner_phone" type="tel" placeholder="Số điện thoại" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]" required>
                <input v-model="form.owner_email" type="email" placeholder="Địa chỉ email (không bắt buộc)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Thông tin thú cưng</label>
                <input v-model="form.pet_name" type="text" placeholder="Tên thú cưng" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]" required>
                <input v-model="form.weight" type="number" step="0.1" placeholder="Cân nặng (kg) (không bắt buộc)" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                <select v-model="form.species_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]" required>
                    <option value="" disabled>Chọn loài</option>
                    <option v-for="sp in species" :key="sp.id" :value="sp.id">{{ sp.name }}</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phân công bác sĩ (không bắt buộc)</label>
                <select v-model="form.doctor_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                    <option value="">Tự động phân công sau</option>
                    <option v-for="doc in doctors" :key="doc.id" :value="doc.id">BS. {{ doc.user?.name || `Bác sĩ #${doc.id}` }}</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tình trạng</label>
                <select v-model="conditionOption" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                    <option value="">Chọn tình trạng</option>
                    <option value="Khám tổng quát">Khám tổng quát</option>
                    <option value="Tiêm phòng">Tiêm phòng</option>
                    <option value="Vấn đề tiêu hóa">Vấn đề tiêu hóa</option>
                    <option value="Vấn đề da liễu">Vấn đề da liễu</option>
                    <option value="Chấn thương">Chấn thương</option>
                    <option value="Other">Khác (tự nhập bên dưới)</option>
                </select>
                <input v-model="conditionCustom" :readonly="!isCustomCondition" type="text" :placeholder="isCustomCondition ? 'Nhập chi tiết tình trạng' : 'Ghi chú thêm (tùy chọn)'" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
            </div>

            <div class="flex items-center gap-2">
                <input v-model="form.is_emergency" type="checkbox" id="is_emergency" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                <label for="is_emergency" class="text-sm font-medium text-red-600">Đánh dấu khẩn cấp</label>
            </div>

            <button type="submit" :disabled="isSubmitting" class="w-full rounded-xl bg-[#2A6496] px-4 py-3 font-semibold text-white transition hover:bg-[#235780] disabled:opacity-50">
                {{ isSubmitting ? 'Đang xử lý...' : 'Đăng ký và thêm vào hàng chờ' }}
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
        notifySuccess('Đăng ký khách vãng lai thành công!');
        
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
        
        // Redirect to dashboard or appointments to see the queue
        setTimeout(() => {
            window.location.href = '/receptionist/dashboard';
        }, 1500);
    } catch (e) {
        handleApiError(e);
    } finally {
        isSubmitting.value = false;
    }
}

onMounted(() => {
    loadInitialData().finally(() => {
        isLoading.value = false;
    });
});
</script>
