<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-6">
      <!-- Section header with gradient line -->
      <div class="border-b border-slate-100 pb-4">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">📅 Đặt lịch hẹn khám bệnh</h1>
        <p class="text-sm text-slate-400 mt-1">Đăng ký lịch khám với các bác sĩ chuyên khoa thú y hàng đầu của PetCare.</p>
      </div>

      <section class="grid gap-8 lg:grid-cols-5">
        <!-- Guidance & Pet Check Panel (2/5 columns) -->
        <article class="lg:col-span-2 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur flex flex-col justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
              Hướng dẫn đặt lịch
            </h2>
            <p class="text-xs text-slate-400 leading-relaxed">
              Để đảm bảo quy trình khám được chuẩn xác nhất, xin vui lòng kiểm tra và cập nhật hồ sơ các bé cưng trước khi tiến hành chọn thời gian khám bệnh.
            </p>

            <!-- Interactive Pet Checklist card -->
            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-4 transition-all duration-300 hover:border-slate-300">
              <div class="flex items-start gap-3">
                <span class="text-2xl">🐕</span>
                <div>
                  <h4 class="text-sm font-bold text-slate-700">Kiểm tra thông tin thú cưng</h4>
                  <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Bạn có thể cập nhật cân nặng, lịch sử tiêm phòng hoặc các dấu hiệu dị ứng thức ăn cho bé để bác sĩ có dữ liệu khám chính xác.
                  </p>
                </div>
              </div>
              <a
                href="/owner/pets"
                class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-indigo-50 px-4 py-2.5 text-xs font-bold text-indigo-600 hover:bg-indigo-100 transition-all duration-300"
              >
                🐾 Quản lý hồ sơ bé cưng
              </a>
            </div>
          </div>

          <!-- Bottom helpful prompt -->
          <div class="mt-6 p-4 rounded-2xl bg-amber-50/40 border border-amber-100/50 flex gap-3 text-xs text-amber-700">
            <span class="text-lg">💡</span>
            <p class="leading-relaxed">
              Bạn có thể trò chuyện trực tiếp với <strong>Trợ lý AI PetCare</strong> qua ô chat bên cạnh để tham khảo ý kiến về triệu chứng trước khi chọn giờ khám nhé!
            </p>
          </div>
        </article>

        <!-- Appointment Creation & List Panel (3/5 columns) -->
        <article class="lg:col-span-3 rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur">
          <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
            Tạo yêu cầu lịch hẹn
          </h2>
          <p :class="statusClass" class="text-xs leading-relaxed mb-4">{{ statusMessage }}</p>

          <form @submit.prevent="createAppointment" class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Chọn bé cưng khám</label>
              <select v-model="form.pet_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                <option value="">Chọn thú cưng từ danh sách của bạn</option>
                <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                  🐾 {{ pet.name }}{{ pet.species?.name ? ` (${translateSpecies(pet.species.name)})` : '' }}
                </option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Dịch vụ y tế chỉ định</label>
              <select v-model="form.service_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                <option value="">Chọn dịch vụ khám thú cưng</option>
                <option v-for="service in services" :key="service.id" :value="service.id">
                  🩺 {{ service.name }} - {{ Number(service.price).toLocaleString('vi-VN') }}đ ({{ service.duration_minutes }} phút)
                </option>
              </select>
            </div>

            <div>
              <label class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Ngày hẹn khám</label>
              <input v-model="form.appointment_date" required type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
            </div>

            <div>
              <label class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Thời gian (Giờ & Phút)</label>
              <div class="grid grid-cols-2 gap-2">
                <select v-model="form.appointment_hour" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                  <option value="08">08 Giờ</option>
                  <option value="09">09 Giờ</option>
                  <option value="10">10 Giờ</option>
                  <option value="11">11 Giờ</option>
                  <option value="12">12 Giờ</option>
                  <option value="13">13 Giờ</option>
                  <option value="14">14 Giờ</option>
                  <option value="15">15 Giờ</option>
                  <option value="16">16 Giờ</option>
                  <option value="17">17 Giờ</option>
                  <option value="18">18 Giờ</option>
                  <option value="19">19 Giờ</option>
                </select>
                <select v-model="form.appointment_minute" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
                  <option value="00">00 Phút</option>
                  <option value="15">15 Phút</option>
                  <option value="30">30 Phút</option>
                  <option value="45">45 Phút</option>
                </select>
              </div>
            </div>

            <div class="sm:col-span-2">
              <label class="mb-1 block text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Lý do & Triệu chứng khám</label>
              <textarea v-model="form.reason" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" placeholder="Mô tả các biểu hiện bất thường, bỏ ăn, mệt mỏi của bé..."></textarea>
            </div>

            <div class="sm:col-span-2 pt-2">
              <button type="submit" :disabled="isSaving" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition-all duration-300 hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:opacity-50">
                {{ isSaving ? 'Đang tạo...' : '✨ Đăng ký lịch hẹn khám' }}
              </button>
            </div>
          </form>

          <!-- Upcoming appointments sub-panel -->
          <div class="mt-8 pt-6 border-t border-slate-100">
            <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-slate-400 mb-4 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              Danh sách lịch hẹn sắp tới
            </h3>
            
            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
              <div v-if="appointments.length === 0" class="flex flex-col items-center justify-center py-8 text-center bg-slate-50/30 rounded-2xl border border-dashed border-slate-100">
                <span class="text-3xl mb-1.5">📅</span>
                <p class="text-xs font-bold text-slate-500">Bạn chưa có lịch hẹn khám nào.</p>
              </div>
              
              <div v-for="appointment in appointments" :key="appointment.id" class="appointment-item flex items-center justify-between p-4 rounded-2xl border border-slate-100 bg-[#FCFDFE] hover:border-slate-200 transition-all duration-300">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-extrabold text-slate-500">
                    {{ appointment.pet?.name ? appointment.pet.name.charAt(0).toUpperCase() : '🐈' }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-extrabold text-slate-700">{{ appointment.pet?.name ?? 'Không rõ' }}</p>
                      <span :class="getStatusClass(appointment.status)" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                        {{ getStatusLabel(appointment.status) }}
                      </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                      {{ formatAppointmentDate(appointment.appointment_at) }}
                    </p>
                    <p class="text-[11px] text-indigo-500 font-bold mt-1">Dịch vụ: {{ appointment.service?.name || 'Khám tổng quát' }}</p>
                    <p v-if="appointment.reason" class="text-[11px] text-slate-400 mt-0.5 leading-relaxed"><span class="font-semibold text-slate-500">Lý do:</span> {{ appointment.reason }}</p>
                  </div>
                </div>

                <button v-if="appointment.status === 'pending' || appointment.status === 'confirmed'" @click="cancelAppointment(appointment.id)" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-500 hover:border-rose-500 hover:text-rose-600 transition-all duration-200">
                  ❌ Hủy
                </button>
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
const appointments = ref([]);
const services = ref([]);

const statusMessage = ref('Đang tải danh sách...');
const statusClass = ref('mt-2 text-xs text-slate-400');

const form = reactive({
    pet_id: '',
    service_id: '',
    appointment_date: '',
    appointment_hour: '09',
    appointment_minute: '00',
    reason: ''
});

function translateSpecies(name) {
    if (!name) return '';
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

function setStatus(message, kind = 'neutral') {
    statusMessage.value = message;
    const classMap = {
        neutral: 'mt-2 text-xs text-slate-400',
        success: 'mt-2 text-xs text-emerald-600 font-bold',
        error: 'mt-2 text-xs text-rose-600 font-bold',
    };
    statusClass.value = classMap[kind];
}

function formatAppointmentDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString('vi-VN', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getStatusLabel(status) {
  switch (status) {
    case 'pending': return 'Chờ duyệt';
    case 'confirmed': return 'Đã xác nhận';
    case 'completed': return 'Đã xong';
    case 'cancelled': return 'Đã hủy';
    default: return status;
  }
}

function getStatusClass(status) {
  switch (status) {
    case 'pending': return 'bg-amber-50 text-amber-600 border-amber-100';
    case 'confirmed': return 'bg-sky-50 text-sky-600 border-sky-100';
    case 'completed': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    case 'cancelled': return 'bg-rose-50 text-rose-600 border-rose-100';
    default: return 'bg-slate-50 text-slate-500 border-slate-100';
  }
}

async function loadData() {
    isLoading.value = true;
    try {
        const [petsRes, appointmentsRes, servicesRes] = await Promise.all([
            callApi('/api/owner/pets', 'GET'),
            callApi('/api/owner/appointments', 'GET'),
            callApi('/api/services', 'GET')
        ]);
        
        pets.value = petsRes.data || [];
        appointments.value = appointmentsRes.data || [];
        services.value = servicesRes.data || [];
        
        if (pets.value.length === 0) {
            setStatus('Bạn chưa đăng ký bé cưng nào. Hãy tạo hồ sơ thú cưng trước.', 'error');
        } else {
            setStatus(`Đã tải thành công ${pets.value.length} bé cưng của bạn.`, 'success');
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
            service_id: Number(form.service_id),
            appointment_date: form.appointment_date,
            appointment_time: `${form.appointment_hour}:${form.appointment_minute}`,
            reason: form.reason || null,
        };
        
        await callApi('/api/owner/appointments', 'POST', payload);
        
        // Reset form but keep hour/minute defaults
        form.pet_id = '';
        form.service_id = '';
        form.appointment_date = '';
        form.reason = '';
        
        setStatus('Tạo lịch hẹn thành công và đang chờ duyệt.', 'success');
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
    if (!window.confirm('Bạn có chắc chắn muốn hủy lịch hẹn khám này không?')) return;
    try {
        await callApi(`/api/owner/appointments/${id}`, 'DELETE');
        setStatus('Đã hủy lịch hẹn thành công.', 'success');
        notifySuccess('Đã hủy lịch hẹn thành công!');
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

<style scoped>
.appointment-item {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.appointment-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.02);
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
