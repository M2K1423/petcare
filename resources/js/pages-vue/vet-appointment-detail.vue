<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Khám và bệnh án</h1>
            <p class="text-sm text-[#4A4A4A]">Xem hồ sơ ca bệnh và lưu bệnh án sau khi khám.</p>
        </div>
        <a href="/vet/appointments" class="inline-flex items-center rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] shadow-sm hover:border-[#2A6496] hover:text-[#2A6496]">
            Quay lại lịch hẹn
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div class="space-y-4 text-sm text-[#4A4A4A]">
                <div v-if="!appointment" class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-[#B91C1C]">Không thể tải chi tiết lịch hẹn.</div>
                
                <div v-else class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Thú cưng:</span> {{ appointment.pet?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Loài:</span> {{ appointment.pet?.species?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Giống:</span> {{ appointment.pet?.breed ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Chủ nuôi:</span> {{ appointment.owner?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Điện thoại:</span> {{ appointment.owner?.phone ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Email:</span> {{ appointment.owner?.email ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Bác sĩ:</span> {{ appointment.doctor?.user?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Dịch vụ:</span> {{ appointment.service?.name ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Thời gian:</span> {{ formatDateTime(appointment.appointment_at) }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Trạng thái:</span> {{ workflowLabel(appointment.workflow_status) }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Lý do:</span> {{ appointment.reason ?? 'Chưa có' }}</div>
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Mã bệnh án:</span> {{ appointment.medical_record?.record_code ?? 'Chưa tạo' }}</div>
                    
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2">
                        <span class="font-semibold text-[#333333]">Lịch sử tiêm phòng:</span>
                        <ul class="mt-2 space-y-1">
                            <li v-if="vaccinations.length === 0" class="text-xs text-[#64748B]">Chưa có lịch sử tiêm phòng.</li>
                            <li v-else v-for="item in vaccinations" :key="item.id" class="text-xs text-[#4A4A4A]">
                                {{ item.vaccine_name ?? 'Vắc xin' }} • {{ item.vaccinated_on ?? 'Chưa có' }}{{ item.next_due_on ? ` • Mũi sau: ${item.next_due_on}` : '' }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2">
                        <span class="font-semibold text-[#333333]">Lần khám trước:</span>
                        <ul class="mt-2 space-y-1">
                            <li v-if="previousRecords.length === 0" class="text-xs text-[#64748B]">Chưa có lần khám trước.</li>
                            <li v-else v-for="record in previousRecords" :key="record.id" class="text-xs text-[#4A4A4A]">
                                {{ record.record_code ?? 'Chưa có' }} • {{ record.record_date ?? 'Chưa có' }} • {{ record.final_diagnosis ?? record.diagnosis ?? 'Chưa có' }}
                            </li>
                        </ul>
                    </div>
                    
                    <div v-if="(appointment.workflow_status ?? 'awaiting_exam') === 'awaiting_exam'" class="sm:col-span-2">
                        <button @click="acceptCase" :disabled="isAccepting" type="button" class="inline-flex items-center rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F] disabled:opacity-50 transition">
                            {{ isAccepting ? 'Đang nhận...' : 'Nhận ca khám' }}
                        </button>
                    </div>
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Lưu bệnh án</h2>
            <form @submit.prevent="saveMedicalRecord" class="mt-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="record-temperature" class="text-sm font-semibold text-[#333333]">Nhiệt độ (C)</label>
                        <input v-model.number="form.temperature_c" id="record-temperature" type="number" step="0.1" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                    <div>
                        <label for="record-weight" class="text-sm font-semibold text-[#333333]">Cân nặng (kg)</label>
                        <input v-model.number="form.weight_kg" id="record-weight" type="number" step="0.01" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                    <div>
                        <label for="record-heart-rate" class="text-sm font-semibold text-[#333333]">Nhịp tim (bpm)</label>
                        <input v-model.number="form.heart_rate_bpm" id="record-heart-rate" type="number" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                </div>
                <div>
                    <label for="record-date" class="text-sm font-semibold text-[#333333]">Ngày ghi nhận</label>
                    <input v-model="form.record_date" id="record-date" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>
                <div>
                    <label for="record-symptoms" class="text-sm font-semibold text-[#333333]">Triệu chứng</label>
                    <textarea v-model="form.symptoms" id="record-symptoms" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-abnormal-signs" class="text-sm font-semibold text-[#333333]">Dấu hiệu bất thường</label>
                    <textarea v-model="form.abnormal_signs" id="record-abnormal-signs" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-preliminary-diagnosis" class="text-sm font-semibold text-[#333333]">Chẩn đoán sơ bộ</label>
                    <textarea v-model="form.preliminary_diagnosis" id="record-preliminary-diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-diagnosis" class="text-sm font-semibold text-[#333333]">Chẩn đoán</label>
                    <textarea v-model="form.diagnosis" id="record-diagnosis" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
                </div>
                <div>
                    <label for="record-final-diagnosis" class="text-sm font-semibold text-[#333333]">Chẩn đoán cuối cùng</label>
                    <textarea v-model="form.final_diagnosis" id="record-final-diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="record-pathology" class="text-sm font-semibold text-[#333333]">Bệnh lý</label>
                        <input v-model="form.pathology" id="record-pathology" type="text" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder="Viêm da, Parvo, nhiễm khuẩn...">
                    </div>
                    <div>
                        <label for="record-severity" class="text-sm font-semibold text-[#333333]">Mức độ bệnh</label>
                        <select v-model="form.severity_level" id="record-severity" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                            <option value="">Chọn mức độ</option>
                            <option value="mild">Nhẹ</option>
                            <option value="moderate">Trung bình</option>
                            <option value="severe">Nặng</option>
                            <option value="critical">Nguy kịch</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="record-prescription" class="text-sm font-semibold text-[#333333]">Đơn thuốc</label>
                    <textarea v-model="form.prescription" id="record-prescription" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
                </div>
                <div>
                    <label for="record-treatment-protocol" class="text-sm font-semibold text-[#333333]">Phác đồ điều trị</label>
                    <textarea v-model="form.treatment_protocol" id="record-treatment-protocol" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-disease-progress" class="text-sm font-semibold text-[#333333]">Tiến triển bệnh</label>
                    <textarea v-model="form.disease_progress" id="record-disease-progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-follow-up-plan" class="text-sm font-semibold text-[#333333]">Kế hoạch tái khám / theo dõi</label>
                    <textarea v-model="form.follow_up_plan" id="record-follow-up-plan" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="record-workflow-status" class="text-sm font-semibold text-[#333333]">Trạng thái ca khám</label>
                        <select v-model="form.workflow_status" id="record-workflow-status" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                            <option value="awaiting_exam">Chờ khám</option>
                            <option value="examining">Đang khám</option>
                            <option value="awaiting_lab">Chờ xét nghiệm</option>
                            <option value="treating">Đang điều trị</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="follow_up">Tái khám</option>
                        </select>
                    </div>
                    <div>
                        <label for="record-follow-up-at" class="text-sm font-semibold text-[#333333]">Ngày tái khám</label>
                        <input v-model="form.follow_up_at" id="record-follow-up-at" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                </div>
                <div>
                    <label for="record-service-orders" class="text-sm font-semibold text-[#333333]">Chỉ định dịch vụ / xét nghiệm (JSON)</label>
                    <textarea v-model="form.service_orders" id="record-service-orders" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"name":"Xét nghiệm máu","status":"ordered","result":null}]'></textarea>
                </div>
                <div>
                    <label for="record-prescriptions-list" class="text-sm font-semibold text-[#333333]">Đơn thuốc chi tiết (JSON)</label>
                    <textarea v-model="form.prescriptions" id="record-prescriptions-list" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"medicine_name":"Amoxicillin","dosage":"2 lần/ngày","days":5,"instructions":"Sau ăn"}]'></textarea>
                </div>
                <div>
                    <label for="record-procedures" class="text-sm font-semibold text-[#333333]">Thủ thuật / điều trị (JSON)</label>
                    <textarea v-model="form.procedures" id="record-procedures" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"name":"Truyền dịch","status":"done","notes":"500ml"}]'></textarea>
                </div>
                <div>
                    <label for="record-progress-logs" class="text-sm font-semibold text-[#333333]">Theo dõi nội trú / tiến triển (JSON)</label>
                    <textarea v-model="form.progress_logs" id="record-progress-logs" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"noted_at":"2026-04-27 09:00:00","note":"Ổn định","vitals":"T=38.5"}]'></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input v-model="form.sign_off" id="record-sign-off" type="checkbox" class="h-4 w-4 rounded border-[#C1C4C9] text-[#2A6496] focus:ring-[#2A6496]">
                    <label for="record-sign-off" class="text-sm font-semibold text-[#333333]">Ký xác nhận chuyên môn</label>
                </div>
                <div>
                    <label for="record-notes" class="text-sm font-semibold text-[#333333]">Ghi chú</label>
                    <textarea v-model="form.notes" id="record-notes" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <button type="submit" :disabled="isSaving" class="inline-flex w-full items-center justify-center rounded-2xl border border-[#2A6496] bg-[#2A6496] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#235780] disabled:opacity-50">
                    {{ isSaving ? 'Đang lưu...' : 'Lưu bệnh án' }}
                </button>
            </form>
        </article>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const isAccepting = ref(false);
const isSaving = ref(false);
const appointmentId = ref(null);

const appointment = ref(null);
const vaccinations = ref([]);
const previousRecords = ref([]);

const form = reactive({
    record_date: '',
    temperature_c: null,
    weight_kg: null,
    heart_rate_bpm: null,
    symptoms: '',
    abnormal_signs: '',
    preliminary_diagnosis: '',
    diagnosis: '',
    final_diagnosis: '',
    pathology: '',
    severity_level: '',
    prescription: '',
    treatment_protocol: '',
    disease_progress: '',
    follow_up_plan: '',
    service_orders: '',
    prescriptions: '',
    procedures: '',
    progress_logs: '',
    sign_off: false,
    notes: '',
    workflow_status: 'completed',
    follow_up_at: ''
});

function formatDateTime(input) {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function workflowLabel(status) {
    const map = {
        awaiting_exam: 'Chờ khám',
        examining: 'Đang khám',
        awaiting_lab: 'Chờ xét nghiệm',
        treating: 'Đang điều trị',
        completed: 'Hoàn thành',
        follow_up: 'Tái khám',
    };
    if (!status) return 'Chờ khám';
    return map[status] ?? status;
}

function stringifyJson(value) {
    if (!value) return '';
    return JSON.stringify(value, null, 2);
}

function parseJsonArray(value) {
    const trimmed = value.trim();
    if (!trimmed) return null;
    const parsed = JSON.parse(trimmed);
    if (!Array.isArray(parsed)) {
        throw new Error('Trường JSON phải là một mảng.');
    }
    return parsed;
}

function fillRecordForm(record, dataStatus, dataFollowUp) {
    const today = new Date().toISOString().slice(0, 10);
    form.record_date = record?.record_date ?? today;
    form.temperature_c = record?.temperature_c ?? null;
    form.weight_kg = record?.weight_kg ?? null;
    form.heart_rate_bpm = record?.heart_rate_bpm ?? null;
    form.symptoms = record?.symptoms ?? '';
    form.abnormal_signs = record?.abnormal_signs ?? '';
    form.preliminary_diagnosis = record?.preliminary_diagnosis ?? '';
    form.diagnosis = record?.diagnosis ?? '';
    form.final_diagnosis = record?.final_diagnosis ?? '';
    form.pathology = record?.pathology ?? '';
    form.severity_level = record?.severity_level ?? '';
    form.prescription = record?.prescription ?? record?.treatment ?? '';
    form.treatment_protocol = record?.treatment_protocol ?? '';
    form.disease_progress = record?.disease_progress ?? '';
    form.follow_up_plan = record?.follow_up_plan ?? '';
    form.service_orders = stringifyJson(record?.service_orders);
    form.prescriptions = stringifyJson(record?.prescriptions);
    form.procedures = stringifyJson(record?.procedures);
    form.progress_logs = stringifyJson(record?.progress_logs);
    form.sign_off = Boolean(record?.signed_off_at);
    form.notes = record?.notes ?? '';
    form.workflow_status = dataStatus ?? 'awaiting_exam';
    form.follow_up_at = dataFollowUp?.slice(0, 10) ?? '';
}

async function acceptCase() {
    isAccepting.value = true;
    try {
        await callApi(`/api/vet/appointments/${appointmentId.value}/accept`, 'PATCH');
        notifySuccess('Đã tiếp nhận ca khám.');
        await loadAppointment();
    } catch (error) {
        handleApiError(error);
    } finally {
        isAccepting.value = false;
    }
}

async function saveMedicalRecord() {
    isSaving.value = true;
    try {
        const payload = {
            record_date: form.record_date || null,
            temperature_c: form.temperature_c,
            weight_kg: form.weight_kg,
            heart_rate_bpm: form.heart_rate_bpm,
            symptoms: form.symptoms || null,
            abnormal_signs: form.abnormal_signs || null,
            preliminary_diagnosis: form.preliminary_diagnosis || null,
            diagnosis: form.diagnosis || '',
            final_diagnosis: form.final_diagnosis || null,
            pathology: form.pathology || null,
            severity_level: form.severity_level || null,
            prescription: form.prescription || '',
            treatment_protocol: form.treatment_protocol || null,
            disease_progress: form.disease_progress || null,
            follow_up_plan: form.follow_up_plan || null,
            service_orders: parseJsonArray(form.service_orders),
            prescriptions: parseJsonArray(form.prescriptions),
            procedures: parseJsonArray(form.procedures),
            progress_logs: parseJsonArray(form.progress_logs),
            workflow_status: form.workflow_status || 'completed',
            follow_up_at: form.follow_up_at || null,
            sign_off: form.sign_off,
            notes: form.notes || null,
        };

        await callApi(`/api/vet/appointments/${appointmentId.value}/medical-record`, 'PUT', payload);
        notifySuccess('Đã lưu bệnh án thành công.');
        await loadAppointment();
    } catch (error) {
        if (error.message.includes('Trường JSON')) {
            notifyError(error.message);
        } else {
            handleApiError(error);
        }
    } finally {
        isSaving.value = false;
    }
}

async function loadAppointment() {
    try {
        const response = await callApi(`/api/vet/appointments/${appointmentId.value}`, 'GET');
        appointment.value = response.data;
        vaccinations.value = response.meta?.vaccination_history ?? [];
        previousRecords.value = response.meta?.previous_medical_records ?? [];
        
        fillRecordForm(response.data.medical_record, response.data.workflow_status, response.data.follow_up_at);
    } catch (error) {
        handleApiError(error);
        appointment.value = null;
    }
}

onMounted(() => {
    const root = document.querySelector('[data-page="vet-appointment-detail"]');
    if (root) {
        appointmentId.value = Number(root.getAttribute('data-appointment-id'));
        if (appointmentId.value) {
            loadAppointment().finally(() => {
                isLoading.value = false;
            });
        } else {
            isLoading.value = false;
        }
    }
});
</script>
