<template>
  <template v-if="!isLoading">
    <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">{{ sectionTitle }}</h1>
            <p class="text-sm text-[#4A4A4A]">{{ sectionDescription }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 text-xs text-[#4A4A4A]">
            <span class="rounded-full border border-[#DDE1E6] bg-white px-3 py-1">Vai trò: Bác sĩ</span>
            <span class="rounded-full border border-[#DDE1E6] bg-white px-3 py-1">12 mô-đun</span>
        </div>
    </div>

    <section v-if="sectionKey === 'dashboard'" class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="space-y-6">
            <div v-if="!summary" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Đang tải tổng quan...</div>
            <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] text-[#2A6496] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Ca hôm nay</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_total }}</p>
                </article>
                <article class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] text-[#92400E] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Chờ khám</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_waiting_exam }}</p>
                </article>
                <article class="rounded-2xl border border-[#FEF3C7] bg-[#FFFBEB] text-[#92400E] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Đang khám</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_examining }}</p>
                </article>
                <article class="rounded-2xl border border-[#FED7AA] bg-[#FFF7ED] text-[#C2410C] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Chờ xét nghiệm</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_awaiting_lab }}</p>
                </article>
                <article class="rounded-2xl border border-[#DDD6FE] bg-[#F5F3FF] text-[#5B21B6] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Đang điều trị</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_treating }}</p>
                </article>
                <article class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] text-[#027A48] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Hoàn thành</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.today_completed }}</p>
                </article>
                <article class="rounded-2xl border border-[#BFDBFE] bg-[#EFF6FF] text-[#1D4ED8] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Lịch tái khám</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.follow_up_upcoming }}</p>
                </article>
                <article class="rounded-2xl border border-[#FBCFE8] bg-[#FDF2F8] text-[#9D174D] px-4 py-4">
                    <p class="text-xs uppercase tracking-[0.14em]">Nội trú theo dõi</p>
                    <p class="mt-1 text-2xl font-extrabold">{{ summary.inpatient_cases }}</p>
                </article>
            </div>
            
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Ca sắp tới</h2>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div v-if="quickAppointments.length === 0" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có lịch hẹn được phân công.</div>
                    
                    <article v-for="item in quickAppointments" :key="item.id" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-[#333333]">{{ item.pet?.name ?? 'Thú cưng chưa rõ' }}</p>
                                <p class="mt-1 text-xs text-[#4A4A4A]">{{ item.owner?.name ?? 'N/A' }} • {{ toLocalDate(item.appointment_at) }}</p>
                            </div>
                            <span :class="['rounded-full px-2 py-1 text-[11px] font-semibold', workflowTone(item.workflow_status)]">{{ workflowText(item.workflow_status) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a :href="`/vet/appointments/${item.id}`" class="rounded-lg border border-[#2A6496] bg-[#2A6496] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#235780]">Mở ca</a>
                            <a href="/vet/schedule-week" class="rounded-lg border border-[#C1C4C9] bg-white px-3 py-1.5 text-xs font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">Xem tuần</a>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </section>
    
    <section v-else-if="sectionKey === 'schedule'" class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="grid gap-6 xl:grid-cols-2">
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Lịch hẹn hôm nay</h2>
                <div class="mt-3 space-y-3">
                    <div v-if="todayAppointments.length === 0" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có lịch hẹn hôm nay.</div>
                    <article v-for="item in todayAppointments" :key="item.id" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-[#333333]">{{ item.pet?.name ?? 'Thú cưng chưa rõ' }}</p>
                                <p class="mt-1 text-xs text-[#4A4A4A]">{{ item.owner?.name ?? 'Chưa có' }} • {{ toLocalDate(item.appointment_at) }}</p>
                            </div>
                            <span :class="['rounded-full px-2 py-1 text-[11px] font-semibold', workflowTone(item.workflow_status)]">{{ workflowText(item.workflow_status) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button @click="acceptCase(item.id)" :disabled="isAccepting === item.id" class="rounded-lg border border-[#0F8A5F] bg-[#0F8A5F] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#0C734F] disabled:opacity-50">Nhận ca</button>
                            <select v-model="item.workflow_status" @change="updateWorkflowStatus(item.id, item.workflow_status)" class="rounded-lg border border-[#C1C4C9] bg-white px-2 py-1.5 text-xs text-[#333333]">
                                <option value="awaiting_exam">Chờ khám</option>
                                <option value="examining">Đang khám</option>
                                <option value="awaiting_lab">Chờ xét nghiệm</option>
                                <option value="treating">Đang điều trị</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="follow_up">Tái khám</option>
                            </select>
                            <a :href="`/vet/appointments/${item.id}`" class="rounded-lg border border-[#2A6496] bg-[#2A6496] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#235780]">Mở</a>
                        </div>
                    </article>
                </div>
            </section>
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Thú cưng chờ khám</h2>
                <div class="mt-3 space-y-3">
                    <div v-if="waitingAppointments.length === 0" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có ca chờ khám.</div>
                    <article v-for="item in waitingAppointments" :key="item.id" class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-[#333333]">{{ item.pet?.name ?? 'Thú cưng chưa rõ' }}</p>
                                <p class="mt-1 text-xs text-[#4A4A4A]">{{ item.owner?.name ?? 'Chưa có' }} • {{ toLocalDate(item.appointment_at) }}</p>
                            </div>
                            <span :class="['rounded-full px-2 py-1 text-[11px] font-semibold', workflowTone(item.workflow_status)]">{{ workflowText(item.workflow_status) }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button @click="acceptCase(item.id)" :disabled="isAccepting === item.id" class="rounded-lg border border-[#0F8A5F] bg-[#0F8A5F] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#0C734F] disabled:opacity-50">Nhận ca</button>
                            <select v-model="item.workflow_status" @change="updateWorkflowStatus(item.id, item.workflow_status)" class="rounded-lg border border-[#C1C4C9] bg-white px-2 py-1.5 text-xs text-[#333333]">
                                <option value="awaiting_exam">Chờ khám</option>
                                <option value="examining">Đang khám</option>
                                <option value="awaiting_lab">Chờ xét nghiệm</option>
                                <option value="treating">Đang điều trị</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="follow_up">Tái khám</option>
                            </select>
                            <a :href="`/vet/appointments/${item.id}`" class="rounded-lg border border-[#2A6496] bg-[#2A6496] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#235780]">Mở</a>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </section>

    <section v-else class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">
                <h2 class="text-lg font-bold text-[#333333]">{{ currentModule.title }}</h2>
                <p class="mt-1 text-sm text-[#4A4A4A]">{{ currentModule.desc }}</p>

                <div class="mt-4">
                    <label for="vet-module-appointment" class="text-sm font-semibold text-[#333333]">Lịch hẹn</label>
                    <select v-model="selectedAppointmentId" @change="handleAppointmentSelection" id="vet-module-appointment" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none focus:border-[#2A6496]">
                        <option v-if="appointments.length === 0" value="">Không có lịch hẹn được phân công</option>
                        <option v-for="item in appointments" :key="item.id" :value="item.id">
                            {{ item.id }} • {{ item.pet?.name ?? 'Thú cưng chưa rõ' }} • {{ workflowText(item.workflow_status) }}
                        </option>
                    </select>
                </div>

                <form @submit.prevent="saveModule" class="mt-4 space-y-4">
                    <div v-html="currentModule.fieldsHtml"></div>
                    
                    <div class="flex flex-wrap items-center gap-2 mt-4">
                        <button type="submit" :disabled="isSaving" class="rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780] disabled:opacity-50 transition">
                            {{ isSaving ? 'Đang lưu...' : 'Lưu mô-đun' }}
                        </button>
                        <button @click="acceptModuleCase" :disabled="isAccepting" type="button" class="rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F] disabled:opacity-50 transition">
                            {{ isAccepting ? 'Đang nhận...' : 'Nhận ca' }}
                        </button>
                        <button @click="refreshData" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">Làm mới</button>
                        <button @click="printPrescription" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] shadow-sm hover:bg-gray-50 flex items-center gap-2">
                            <Printer class="w-4 h-4" /> In Đơn Thuốc
                        </button>
                    </div>
                </form>
            </article>

            <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-5">
                <h3 class="text-base font-bold text-[#333333]">Bối cảnh ca khám</h3>
                <div class="mt-4 text-sm text-[#4A4A4A]">
                    <p v-if="!selectedDetail" class="text-xs text-[#64748B]">Chọn một lịch hẹn để xem chi tiết.</p>
                    
                    <div v-else class="space-y-3">
                        <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                            <p><span class="font-semibold text-[#333333]">Thú cưng:</span> {{ selectedDetail.data.pet?.name ?? 'Chưa có' }}</p>
                            <p class="mt-1"><span class="font-semibold text-[#333333]">Chủ nuôi:</span> {{ selectedDetail.data.owner?.name ?? 'Chưa có' }} {{ selectedDetail.data.owner?.phone ? `• ${selectedDetail.data.owner.phone}` : '' }}</p>
                            <p class="mt-1"><span class="font-semibold text-[#333333]">Lịch hẹn:</span> {{ toLocalDate(selectedDetail.data.appointment_at) }}</p>
                            <p class="mt-1"><span class="font-semibold text-[#333333]">Quy trình:</span> {{ workflowText(selectedDetail.data.workflow_status) }}</p>
                            <p class="mt-1"><span class="font-semibold text-[#333333]">Mã bệnh án:</span> {{ selectedDetail.data.medical_record?.record_code ?? 'Chưa tạo' }}</p>
                        </div>
                        <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                            <p class="font-semibold text-[#333333]">Lịch sử tiêm phòng</p>
                            <div class="mt-2">
                                <p v-if="!selectedDetail.meta?.vaccination_history || selectedDetail.meta.vaccination_history.length === 0" class="text-xs text-[#64748B]">Không có lịch sử tiêm phòng.</p>
                                <ul v-else class="space-y-1">
                                    <li v-for="(item, idx) in selectedDetail.meta.vaccination_history" :key="idx" class="text-xs text-[#4A4A4A]">
                                        {{ item.vaccine_name ?? 'Vắc xin' }} • {{ item.vaccinated_on ?? 'Chưa có' }} {{ item.next_due_on ? `• mũi tiếp theo: ${item.next_due_on}` : '' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                            <p class="font-semibold text-[#333333]">Lần khám trước</p>
                            <div class="mt-2">
                                <p v-if="!selectedDetail.meta?.previous_medical_records || selectedDetail.meta.previous_medical_records.length === 0" class="text-xs text-[#64748B]">Không có lần khám trước.</p>
                                <ul v-else class="space-y-1">
                                    <li v-for="(record, idx) in selectedDetail.meta.previous_medical_records" :key="idx" class="text-xs text-[#4A4A4A]">
                                        {{ record.record_code ?? 'Chưa có' }} • {{ record.record_date ?? 'Chưa có' }} • {{ record.final_diagnosis ?? record.diagnosis ?? 'Chưa có' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Printer } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const sectionKey = ref('dashboard');
const sectionTitle = ref('Vet Workflow');
const sectionDescription = ref('');

const appointments = ref([]);
const summary = ref(null);
const selectedAppointmentId = ref(null);
const selectedDetail = ref(null);

const isSaving = ref(false);
const isAccepting = ref(false);

function toLocalDate(input) {
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

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function workflowText(value) {
    const map = {
        awaiting_exam: 'Chờ khám',
        examining: 'Đang khám',
        awaiting_lab: 'Chờ xét nghiệm',
        treating: 'Đang điều trị',
        completed: 'Hoàn thành',
        follow_up: 'Tái khám',
    };
    if (!value) return 'Chờ khám';
    return map[value] ?? value;
}

function workflowTone(value) {
    if (value === 'completed') return 'bg-[#ECFDF3] text-[#027A48]';
    if (value === 'follow_up') return 'bg-[#EFF6FF] text-[#1D4ED8]';
    if (value === 'treating') return 'bg-[#EDE9FE] text-[#5B21B6]';
    if (value === 'awaiting_lab') return 'bg-[#FFF7ED] text-[#C2410C]';
    if (value === 'examining') return 'bg-[#FEF3C7] text-[#92400E]';
    return 'bg-[#FFFBEB] text-[#B45309]';
}

const quickAppointments = computed(() => {
    return [...appointments.value]
        .sort((a, b) => a.appointment_at.localeCompare(b.appointment_at))
        .slice(0, 8);
});

const todayAppointments = computed(() => {
    const today = new Date().toISOString().slice(0, 10);
    return appointments.value
        .filter(item => item.appointment_at.slice(0, 10) === today)
        .sort((a, b) => a.appointment_at.localeCompare(b.appointment_at));
});

const waitingAppointments = computed(() => {
    return todayAppointments.value.filter(item => (item.workflow_status ?? 'awaiting_exam') === 'awaiting_exam');
});

const currentModule = computed(() => {
    const medicalRecord = selectedDetail.value?.data?.medical_record ?? {};
    const detailData = selectedDetail.value?.data ?? {};
    
    const sectionMap = {
        intake: {
            title: 'Tiếp nhận và khám lâm sàng',
            desc: 'Nhập nhiệt độ, cân nặng, nhịp tim, triệu chứng và dấu hiệu bất thường.',
            fieldsHtml: `
                <div class="grid gap-4 sm:grid-cols-3">
                    <div><label class="text-sm font-semibold text-[#333333]">Nhiệt độ (C)</label><input name="temperature_c" value="${medicalRecord.temperature_c ?? ''}" type="number" step="0.1" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Cân nặng (kg)</label><input name="weight_kg" value="${medicalRecord.weight_kg ?? ''}" type="number" step="0.01" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Nhịp tim (bpm)</label><input name="heart_rate_bpm" value="${medicalRecord.heart_rate_bpm ?? ''}" type="number" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                </div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Triệu chứng</label><textarea name="symptoms" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.symptoms ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Dấu hiệu bất thường</label><textarea name="abnormal_signs" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.abnormal_signs ?? '')}</textarea></div>
                <input type="hidden" name="workflow_status" value="examining">
            `,
        },
        diagnosis: {
            title: 'Chẩn đoán',
            desc: 'Nhập chẩn đoán sơ bộ, chẩn đoán cuối, bệnh lý và phân mức độ bệnh.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán sơ bộ</label><textarea name="preliminary_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.preliminary_diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán cuối</label><textarea name="final_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.final_diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán chính</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="grid gap-4 sm:grid-cols-2 mt-4">
                    <div><label class="text-sm font-semibold text-[#333333]">Bệnh lý</label><input name="pathology" value="${escapeHtml(medicalRecord.pathology ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Mức độ</label><select name="severity_level" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="">Chọn mức độ</option><option value="mild" ${medicalRecord.severity_level === 'mild' ? 'selected' : ''}>Nhẹ</option><option value="moderate" ${medicalRecord.severity_level === 'moderate' ? 'selected' : ''}>Trung bình</option><option value="severe" ${medicalRecord.severity_level === 'severe' ? 'selected' : ''}>Nặng</option><option value="critical" ${medicalRecord.severity_level === 'critical' ? 'selected' : ''}>Nguy kịch</option></select></div>
                </div>
                <input type="hidden" name="workflow_status" value="examining">
            `,
        },
        records: {
            title: 'Lập bệnh án',
            desc: 'Tạo cập nhật bệnh án, phác đồ điều trị, ghi chú theo dõi, tiến triển bệnh.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Phác đồ điều trị</label><textarea name="treatment_protocol" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.treatment_protocol ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Tiến triển bệnh</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Ghi chú</label><textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.notes ?? '')}</textarea></div>
            `,
        },
        orders: {
            title: 'Chỉ định dịch vụ / xét nghiệm',
            desc: 'Nhập danh sách chỉ định theo JSON. Ví dụ: [{"name":"Xét nghiệm máu","status":"ordered","result":null}]',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chỉ định dịch vụ (JSON)</label><textarea name="service_orders" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.service_orders ?? [], null, 2))}</textarea></div>
                <div class="mt-4"><select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="awaiting_lab" ${(detailData.workflow_status ?? '') === 'awaiting_lab' ? 'selected' : ''}>Chờ xét nghiệm</option><option value="examining">Đang khám</option><option value="treating">Đang điều trị</option></select></div>
            `,
        },
        prescriptions: {
            title: 'Kê đơn thuốc',
            desc: 'Nhập đơn thuốc tổng quát và danh sách chi tiết JSON.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Đơn thuốc tóm tắt</label><textarea name="prescription" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.treatment ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Đơn thuốc chi tiết (JSON)</label><textarea name="prescriptions" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.prescriptions ?? [], null, 2))}</textarea></div>
                <input type="hidden" name="workflow_status" value="treating">
            `,
        },
        procedures: {
            title: 'Điều trị / thủ thuật',
            desc: 'Ghi nhận thủ thuật và can thiệp điều trị theo JSON.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Thủ thuật (JSON)</label><textarea name="procedures" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.procedures ?? [], null, 2))}</textarea></div>
                <div class="mt-4"><select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="treating" ${(detailData.workflow_status ?? '') === 'treating' ? 'selected' : ''}>Đang điều trị</option><option value="completed">Hoàn thành</option></select></div>
            `,
        },
        vaccinations: {
            title: 'Quản lý tiêm phòng',
            desc: 'Theo dõi lịch sử tiêm và lập kế hoạch mũi tiếp theo qua kế hoạch tái khám.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Kế hoạch tiêm phòng và mũi sau</label><textarea name="follow_up_plan" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.follow_up_plan ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Ngày tái khám / nhắc mũi</label><input name="follow_up_at" type="date" value="${escapeHtml(detailData.follow_up_at?.slice(0, 10) ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                <input type="hidden" name="workflow_status" value="follow_up">
            `,
        },
        inpatient: {
            title: 'Theo dõi nội trú',
            desc: 'Nhập log theo dõi và điều chỉnh phác đồ cho ca nội trú.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Tiến triển bệnh</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Nhật ký theo dõi (JSON)</label><textarea name="progress_logs" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.progress_logs ?? [], null, 2))}</textarea></div>
                <input type="hidden" name="workflow_status" value="treating">
            `,
        },
        follow_up: {
            title: 'Tái khám',
            desc: 'Lên lịch tái khám và cập nhật đánh giá hồi phục.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Đánh giá hồi phục</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Kế hoạch tái khám</label><textarea name="follow_up_plan" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.follow_up_plan ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Ngày tái khám</label><input name="follow_up_at" type="date" value="${escapeHtml(detailData.follow_up_at?.slice(0, 10) ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                <input type="hidden" name="workflow_status" value="follow_up">
            `,
        },
        sign_off: {
            title: 'Ký xác nhận chuyên môn',
            desc: 'Duyệt bệnh án và kết luận điều trị.',
            fieldsHtml: `
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Chẩn đoán kết luận</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.final_diagnosis ?? medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="mt-4"><label class="text-sm font-semibold text-[#333333]">Kết luận điều trị</label><textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.notes ?? '')}</textarea></div>
                <div class="mt-4 flex items-center gap-2"><input name="sign_off" value="1" type="checkbox" ${medicalRecord.signed_off_at ? 'checked' : ''} class="h-4 w-4 rounded border-[#C1C4C9]"><label class="text-sm font-semibold text-[#333333]">Ký duyệt hồ sơ</label></div>
                <div class="mt-4"><select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="completed">Hoàn thành</option><option value="follow_up" ${(detailData.workflow_status ?? '') === 'follow_up' ? 'selected' : ''}>Tái khám</option></select></div>
            `,
        },
    };

    return sectionMap[sectionKey.value] || sectionMap['intake'];
});

async function acceptCase(id) {
    if (!id) return;
    isAccepting.value = id;
    try {
        await callApi(`/api/vet/appointments/${id}/accept`, 'PATCH');
        await loadBaseData();
        notifySuccess('Đã nhận ca khám.');
    } catch (error) {
        handleApiError(error);
    } finally {
        isAccepting.value = false;
    }
}

async function acceptModuleCase() {
    if (!selectedAppointmentId.value) {
        notifyError('Vui lòng chọn một lịch hẹn trước.');
        return;
    }
    isAccepting.value = true;
    try {
        await callApi(`/api/vet/appointments/${selectedAppointmentId.value}/accept`, 'PATCH');
        await loadBaseData();
        await loadSelectedDetail();
        notifySuccess('Đã nhận ca khám.');
    } catch (error) {
        handleApiError(error);
    } finally {
        isAccepting.value = false;
    }
}

async function updateWorkflowStatus(id, status) {
    if (!id || !status) return;
    try {
        await callApi(`/api/vet/appointments/${id}/workflow`, 'PATCH', { workflow_status: status });
        await loadBaseData();
    } catch (error) {
        handleApiError(error);
    }
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

async function saveModule(event) {
    if (!selectedAppointmentId.value) {
        notifyError('Vui lòng chọn một lịch hẹn trước.');
        return;
    }

    const form = event.target;
    const formData = new FormData(form);
    const diagnosis = String(formData.get('diagnosis') ?? '').trim();

    if (!diagnosis) {
        notifyError('Cần nhập chẩn đoán.');
        return;
    }

    isSaving.value = true;

    try {
        const payload = {
            diagnosis,
            record_date: new Date().toISOString().slice(0, 10),
            symptoms: String(formData.get('symptoms') ?? '').trim() || null,
            abnormal_signs: String(formData.get('abnormal_signs') ?? '').trim() || null,
            preliminary_diagnosis: String(formData.get('preliminary_diagnosis') ?? '').trim() || null,
            final_diagnosis: String(formData.get('final_diagnosis') ?? '').trim() || null,
            pathology: String(formData.get('pathology') ?? '').trim() || null,
            severity_level: String(formData.get('severity_level') ?? '').trim() || null,
            prescription: String(formData.get('prescription') ?? '').trim() || null,
            treatment_protocol: String(formData.get('treatment_protocol') ?? '').trim() || null,
            disease_progress: String(formData.get('disease_progress') ?? '').trim() || null,
            follow_up_plan: String(formData.get('follow_up_plan') ?? '').trim() || null,
            notes: String(formData.get('notes') ?? '').trim() || null,
            workflow_status: String(formData.get('workflow_status') ?? '').trim() || 'examining',
            follow_up_at: String(formData.get('follow_up_at') ?? '').trim() || null,
            sign_off: formData.get('sign_off') ? true : false,
        };

        const temperature = String(formData.get('temperature_c') ?? '').trim();
        const weight = String(formData.get('weight_kg') ?? '').trim();
        const heartRate = String(formData.get('heart_rate_bpm') ?? '').trim();

        payload.temperature_c = temperature ? Number(temperature) : null;
        payload.weight_kg = weight ? Number(weight) : null;
        payload.heart_rate_bpm = heartRate ? Number(heartRate) : null;

        const serviceOrdersRaw = String(formData.get('service_orders') ?? '').trim();
        const prescriptionsRaw = String(formData.get('prescriptions') ?? '').trim();
        const proceduresRaw = String(formData.get('procedures') ?? '').trim();
        const progressLogsRaw = String(formData.get('progress_logs') ?? '').trim();

        payload.service_orders = serviceOrdersRaw ? parseJsonArray(serviceOrdersRaw) : null;
        payload.prescriptions = prescriptionsRaw ? parseJsonArray(prescriptionsRaw) : null;
        payload.procedures = proceduresRaw ? parseJsonArray(proceduresRaw) : null;
        payload.progress_logs = progressLogsRaw ? parseJsonArray(progressLogsRaw) : null;

        await callApi(`/api/vet/appointments/${selectedAppointmentId.value}/medical-record`, 'PUT', payload);

        await loadBaseData();
        await loadSelectedDetail();
        notifySuccess('Đã lưu thành công.');
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

async function loadBaseData() {
    try {
        const [appointmentsRes, summaryRes] = await Promise.all([
            callApi('/api/vet/appointments', 'GET'),
            callApi('/api/vet/dashboard-summary', 'GET').catch(() => ({ data: null }))
        ]);
        
        appointments.value = appointmentsRes.data ?? [];
        summary.value = summaryRes.data ?? null;
        
        if (!selectedAppointmentId.value && appointments.value.length > 0) {
            selectedAppointmentId.value = appointments.value[0].id;
        }
    } catch (error) {
        handleApiError(error);
    }
}

async function loadSelectedDetail() {
    if (!selectedAppointmentId.value) {
        selectedDetail.value = null;
        return;
    }
    try {
        selectedDetail.value = await callApi(`/api/vet/appointments/${selectedAppointmentId.value}`, 'GET');
    } catch (error) {
        console.error("Failed to load appointment details", error);
        selectedDetail.value = null;
    }
}

async function handleAppointmentSelection() {
    await loadSelectedDetail();
}

async function refreshData() {
    await loadBaseData();
    await loadSelectedDetail();
    notifySuccess('Đã làm mới dữ liệu.');
}

function printPrescription() {
    if (!selectedAppointmentId.value) {
        notifyError('Vui lòng chọn một lịch hẹn trước.');
        return;
    }
    window.open(`/api/vet/appointments/${selectedAppointmentId.value}/prescription/pdf`, '_blank');
}

onMounted(() => {
    const root = document.querySelector('[data-page="vet-workflow"]');
    if (root) {
        sectionKey.value = root.getAttribute('data-section-key') || 'dashboard';
        sectionTitle.value = root.getAttribute('data-section-title') || 'Vet Workflow';
        sectionDescription.value = root.getAttribute('data-section-description') || '';
        
        Promise.all([
            loadBaseData(),
            loadSelectedDetail()
        ]).finally(() => {
            isLoading.value = false;
        });
    } else {
        isLoading.value = false;
    }
});
</script>
