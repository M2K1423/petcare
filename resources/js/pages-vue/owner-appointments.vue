<template>
  <template v-if="!isLoading">
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Thêm thú cưng</h2>
            <p class="mt-2 text-sm text-[#4A4A4A]">Trước khi tạo lịch hẹn, hãy đảm bảo hồ sơ thú cưng của bạn đã có sẵn.</p>

            <div class="mt-4 rounded-2xl border border-dashed border-[#C7CDD5] bg-[#F8FAFC] p-4">
                <p class="text-sm text-[#4A4A4A]">Mở phần quản lý thú cưng để thêm hoặc cập nhật thông tin trước.</p>
                <a
                    href="/owner/pets"
                    class="mt-3 inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]"
                >
                    Đi tới phần thêm thú cưng
                </a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Tạo lịch hẹn</h2>
            <p :class="statusClass">{{ statusMessage }}</p>

            <form @submit.prevent="createAppointment" class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Thú cưng</label>
                    <select v-model="form.pet_id" required class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                        <option value="">Chọn thú cưng từ danh sách của bạn</option>
                        <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                            {{ pet.name }}{{ pet.species?.name ? ` (${pet.species.name})` : '' }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ngày hẹn</label>
                    <input v-model="form.appointment_date" required type="date" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giờ hẹn</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select v-model="form.appointment_hour" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                        <select v-model="form.appointment_minute" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="00">00</option>
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="45">45</option>
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Lý do</label>
                    <textarea v-model="form.reason" rows="4" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Mô tả triệu chứng của thú cưng..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" :disabled="isSaving" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] disabled:opacity-50">
                        {{ isSaving ? 'Đang tạo...' : 'Tạo lịch hẹn' }}
                    </button>
                </div>
            </form>

            <div class="mt-6 rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Lịch hẹn sắp tới</h3>
                <div class="mt-2 space-y-2 text-sm text-[#4A4A4A]">
                    <p v-if="appointments.length === 0">No appointments yet. Create your first appointment above.</p>
                    
                    <div v-for="appointment in appointments" :key="appointment.id" class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-[#333333]">{{ appointment.pet?.name ?? 'Unknown pet' }}{{ appointment.pet?.species?.name ? ` (${appointment.pet.species.name})` : '' }}</p>
                                <p class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">{{ formatAppointmentDate(appointment.appointment_at) }} | {{ appointment.status }}</p>
                                <p v-if="appointment.reason" class="text-xs text-[#4A4A4A]">Reason: {{ appointment.reason }}</p>
                            </div>
                            <button v-if="appointment.status === 'pending' || appointment.status === 'confirmed'" @click="cancelAppointment(appointment.id)" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#B42318] hover:text-[#B42318]">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </article>
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
const pets = ref([]);
const appointments = ref([]);

const statusMessage = ref('Đang tải...');
const statusClass = ref('mt-2 text-sm text-[#4A4A4A]');

const form = reactive({
    pet_id: '',
    appointment_date: '',
    appointment_hour: '09',
    appointment_minute: '00',
    reason: ''
});

function setStatus(message, kind = 'neutral') {
    statusMessage.value = message;
    const classMap = {
        neutral: 'mt-2 text-sm text-[#4A4A4A]',
        success: 'mt-2 text-sm text-emerald-700',
        error: 'mt-2 text-sm text-rose-700',
    };
    statusClass.value = classMap[kind];
}

function formatAppointmentDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString();
}

async function loadData() {
    isLoading.value = true;
    try {
        const [petsRes, appointmentsRes] = await Promise.all([
            callApi('/api/owner/pets', 'GET'),
            callApi('/api/owner/appointments', 'GET')
        ]);
        
        pets.value = petsRes.data || [];
        appointments.value = appointmentsRes.data || [];
        
        if (pets.value.length === 0) {
            setStatus('No pet profile found. Please create a pet first.', 'error');
        } else {
            setStatus(`Loaded ${pets.value.length} pet(s).`, 'success');
        }
    } catch (error) {
        setStatus(error.message, 'error');
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
}

async function createAppointment() {
    isSaving.value = true;
    try {
        const payload = {
            pet_id: Number(form.pet_id),
            appointment_date: form.appointment_date,
            appointment_time: `${form.appointment_hour}:${form.appointment_minute}`,
            reason: form.reason || null,
        };
        
        await callApi('/api/owner/appointments', 'POST', payload);
        
        // Reset form but keep hour/minute defaults
        form.pet_id = '';
        form.appointment_date = '';
        form.reason = '';
        
        setStatus('Appointment created successfully.', 'success');
        notifySuccess('Tạo lịch hẹn thành công!');
        await loadData();
    } catch (error) {
        setStatus(error.message, 'error');
        handleApiError(error);
    } finally {
        isSaving.value = false;
    }
}

async function cancelAppointment(id) {
    if (!window.confirm('Cancel this appointment?')) return;
    try {
        await callApi(`/api/owner/appointments/${id}`, 'DELETE');
        setStatus('Appointment cancelled successfully.', 'success');
        notifySuccess('Đã hủy lịch hẹn!');
        await loadData();
    } catch (error) {
        setStatus(error.message, 'error');
        handleApiError(error);
    }
}

onMounted(() => {
    loadData();
});
</script>
