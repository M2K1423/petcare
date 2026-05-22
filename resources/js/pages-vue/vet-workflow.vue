<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Appointment = {
    id: number;
    appointment_at: string;
    status: string;
    workflow_status?: string | null;
    queue_number?: number | null;
    follow_up_at?: string | null;
    reason?: string | null;
    pet?: { name?: string; species?: { name?: string } } | null;
    owner?: { name?: string; phone?: string | null; email?: string | null } | null;
    service?: { name?: string } | null;
};

type DashboardSummary = {
    today_total: number;
    today_waiting_exam: number;
    today_examining: number;
    today_awaiting_lab: number;
    today_treating: number;
    today_completed: number;
    follow_up_upcoming: number;
    monitoring_records: number;
    inpatient_cases: number;
};

type MedicalRecord = {
    record_code?: string | null;
    temperature_c?: number | null;
    weight_kg?: number | null;
    heart_rate_bpm?: number | null;
    symptoms?: string | null;
    abnormal_signs?: string | null;
    preliminary_diagnosis?: string | null;
    diagnosis?: string | null;
    final_diagnosis?: string | null;
    pathology?: string | null;
    severity_level?: string | null;
    treatment?: string | null;
    treatment_protocol?: string | null;
    disease_progress?: string | null;
    follow_up_plan?: string | null;
    service_orders?: Array<Record<string, unknown>> | null;
    prescriptions?: Array<Record<string, unknown>> | null;
    procedures?: Array<Record<string, unknown>> | null;
    progress_logs?: Array<Record<string, unknown>> | null;
    notes?: string | null;
    record_date?: string | null;
    signed_off_at?: string | null;
};

type AppointmentDetailResponse = {
    data: Appointment & { medical_record?: MedicalRecord | null };
    meta?: {
        vaccination_history?: Array<{
            vaccine_name?: string | null;
            vaccinated_on?: string | null;
            next_due_on?: string | null;
        }>;
        previous_medical_records?: Array<MedicalRecord>;
    };
};

const rootEl = document.getElementById('vet-workflow-root');
const contentEl = document.getElementById('vet-workflow-content');

const sectionKey = rootEl?.getAttribute('data-section-key') || 'dashboard';

let appointments: Appointment[] = [];
let summary: DashboardSummary | null = null;
let selectedAppointmentId: number | null = null;
let selectedDetail: AppointmentDetailResponse | null = null;

function escapeHtml(value: string): string {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function toLocalDate(input: string): string {
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

function workflowText(value?: string | null): string {
    const map: Record<string, string> = {
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

function workflowTone(value?: string | null): string {
    if (value === 'completed') return 'bg-[#ECFDF3] text-[#027A48]';
    if (value === 'follow_up') return 'bg-[#EFF6FF] text-[#1D4ED8]';
    if (value === 'treating') return 'bg-[#EDE9FE] text-[#5B21B6]';
    if (value === 'awaiting_lab') return 'bg-[#FFF7ED] text-[#C2410C]';
    if (value === 'examining') return 'bg-[#FEF3C7] text-[#92400E]';
    return 'bg-[#FFFBEB] text-[#B45309]';
}

function statusBox(title: string, value: number | string, tone: string): string {
    return `
        <article class="rounded-2xl border ${tone} px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em]">${escapeHtml(title)}</p>
            <p class="mt-1 text-2xl font-extrabold">${escapeHtml(String(value))}</p>
        </article>
    `;
}

function appointmentOptionHtml(): string {
    if (appointments.length === 0) {
        return '<option value="">Không có lịch hẹn được phân công</option>';
    }

    return appointments
        .map((item) => {
            const selected = selectedAppointmentId === item.id ? 'selected' : '';
            const label = `${item.id} • ${item.pet?.name ?? 'Thú cưng chưa rõ'} • ${workflowText(item.workflow_status)}`;
            return `<option value="${item.id}" ${selected}>${escapeHtml(label)}</option>`;
        })
        .join('');
}

function sectionFormShell(title: string, description: string, fieldsHtml: string): string {
    return `
        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">
                <h2 class="text-lg font-bold text-[#333333]">${escapeHtml(title)}</h2>
                <p class="mt-1 text-sm text-[#4A4A4A]">${escapeHtml(description)}</p>

                <div class="mt-4">
                    <label for="vet-module-appointment" class="text-sm font-semibold text-[#333333]">Lịch hẹn</label>
                    <select id="vet-module-appointment" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none focus:border-[#2A6496]">
                        ${appointmentOptionHtml()}
                    </select>
                </div>

                <form id="vet-module-form" class="mt-4 space-y-4">
                    ${fieldsHtml}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" class="rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780]">Lưu mô-đun</button>
                        <button id="vet-module-accept" type="button" class="rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F]">Nhận ca</button>
                        <button id="vet-module-refresh" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">Làm mới</button>
                        <button id="vet-module-print" type="button" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 flex items-center gap-2">
                            <i class="fas fa-print"></i> In Đơn Thuốc
                        </button>
                    </div>
                </form>
                <div id="vet-module-status" class="mt-4 hidden rounded-2xl px-4 py-3 text-sm"></div>
            </article>

            <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-5">
                <h3 class="text-base font-bold text-[#333333]">Bối cảnh ca khám</h3>
                <div id="vet-module-context" class="mt-4 text-sm text-[#4A4A4A]">
                    Đang tải...
                </div>
            </article>
        </div>
    `;
}

function setModuleStatus(message: string, tone: 'success' | 'error'): void {
    const statusEl = document.getElementById('vet-module-status');
    if (!statusEl) return;

    statusEl.classList.remove('hidden');
    statusEl.className = `mt-4 rounded-2xl px-4 py-3 text-sm ${tone === 'success' ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF2F2] text-[#B91C1C]'}`;
    statusEl.textContent = message;
}

function buildContextHtml(): string {
    if (!selectedDetail?.data) {
        return '<p class="text-xs text-[#64748B]">Chọn một lịch hẹn để xem chi tiết.</p>';
    }

    const appointment = selectedDetail.data;
    const vaccinationHistory = selectedDetail.meta?.vaccination_history ?? [];
    const previousRecords = selectedDetail.meta?.previous_medical_records ?? [];

    const vaccinationHtml = vaccinationHistory.length === 0
        ? '<p class="text-xs text-[#64748B]">Không có lịch sử tiêm phòng.</p>'
        : `<ul class="space-y-1">${vaccinationHistory.map((item) => `<li class="text-xs text-[#4A4A4A]">${escapeHtml(item.vaccine_name ?? 'Vắc xin')} • ${escapeHtml(item.vaccinated_on ?? 'Chưa có')} ${item.next_due_on ? `• mũi tiếp theo: ${escapeHtml(item.next_due_on)}` : ''}</li>`).join('')}</ul>`;

    const previousHtml = previousRecords.length === 0
        ? '<p class="text-xs text-[#64748B]">Không có lần khám trước.</p>'
        : `<ul class="space-y-1">${previousRecords.map((item) => `<li class="text-xs text-[#4A4A4A]">${escapeHtml(item.record_code ?? 'Chưa có')} • ${escapeHtml(item.record_date ?? 'Chưa có')} • ${escapeHtml(item.final_diagnosis ?? item.diagnosis ?? 'Chưa có')}</li>`).join('')}</ul>`;

    return `
        <div class="space-y-3">
            <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                <p><span class="font-semibold text-[#333333]">Thú cưng:</span> ${escapeHtml(appointment.pet?.name ?? 'Chưa có')}</p>
                <p class="mt-1"><span class="font-semibold text-[#333333]">Chủ nuôi:</span> ${escapeHtml(appointment.owner?.name ?? 'Chưa có')} ${appointment.owner?.phone ? `• ${escapeHtml(appointment.owner.phone)}` : ''}</p>
                <p class="mt-1"><span class="font-semibold text-[#333333]">Lịch hẹn:</span> ${escapeHtml(toLocalDate(appointment.appointment_at))}</p>
                <p class="mt-1"><span class="font-semibold text-[#333333]">Quy trình:</span> ${escapeHtml(workflowText(appointment.workflow_status))}</p>
                <p class="mt-1"><span class="font-semibold text-[#333333]">Mã bệnh án:</span> ${escapeHtml(appointment.medical_record?.record_code ?? 'Chưa tạo')}</p>
            </div>
            <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                <p class="font-semibold text-[#333333]">Lịch sử tiêm phòng</p>
                <div class="mt-2">${vaccinationHtml}</div>
            </div>
            <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-3">
                <p class="font-semibold text-[#333333]">Lần khám trước</p>
                <div class="mt-2">${previousHtml}</div>
            </div>
        </div>
    `;
}

function parseJsonArray(value: string): Array<Record<string, unknown>> | null {
    const trimmed = value.trim();
    if (!trimmed) return null;

    const parsed = JSON.parse(trimmed);
    if (!Array.isArray(parsed)) {
        throw new Error('JSON field must be an array.');
    }

    return parsed as Array<Record<string, unknown>>;
}

function getCurrentMedicalRecord(): MedicalRecord {
    return selectedDetail?.data?.medical_record ?? {};
}

function renderDashboard(): void {
    const quickAppointments = appointments
        .slice()
        .sort((a, b) => a.appointment_at.localeCompare(b.appointment_at))
        .slice(0, 8);

    const summaryHtml = summary
        ? `
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                ${statusBox('Ca hôm nay', summary.today_total, 'border-[#D7E6F5] bg-[#F5FAFF] text-[#2A6496]')}
                ${statusBox('Chờ khám', summary.today_waiting_exam, 'border-[#FDE68A] bg-[#FFFBEB] text-[#92400E]')}
                ${statusBox('Đang khám', summary.today_examining, 'border-[#FEF3C7] bg-[#FFFBEB] text-[#92400E]')}
                ${statusBox('Chờ xét nghiệm', summary.today_awaiting_lab, 'border-[#FED7AA] bg-[#FFF7ED] text-[#C2410C]')}
                ${statusBox('Đang điều trị', summary.today_treating, 'border-[#DDD6FE] bg-[#F5F3FF] text-[#5B21B6]')}
                ${statusBox('Hoàn thành', summary.today_completed, 'border-[#C7EAD8] bg-[#ECFDF3] text-[#027A48]')}
                ${statusBox('Lịch tái khám', summary.follow_up_upcoming, 'border-[#BFDBFE] bg-[#EFF6FF] text-[#1D4ED8]')}
                ${statusBox('Nội trú theo dõi', summary.inpatient_cases, 'border-[#FBCFE8] bg-[#FDF2F8] text-[#9D174D]')}
            </div>
        `
        : '<div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Đang tải tổng quan...</div>';

    const listHtml = quickAppointments.length === 0
        ? '<div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có lịch hẹn được phân công.</div>'
        : quickAppointments.map((item) => `
            <article class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-bold text-[#333333]">${escapeHtml(item.pet?.name ?? 'Thú cưng chưa rõ')}</p>
                        <p class="mt-1 text-xs text-[#4A4A4A]">${escapeHtml(item.owner?.name ?? 'N/A')} • ${escapeHtml(toLocalDate(item.appointment_at))}</p>
                    </div>
                    <span class="rounded-full px-2 py-1 text-[11px] font-semibold ${workflowTone(item.workflow_status)}">${escapeHtml(workflowText(item.workflow_status))}</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="/vet/appointments/${item.id}" class="rounded-lg border border-[#2A6496] bg-[#2A6496] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#235780]">Mở ca</a>
                    <a href="/vet/schedule-week" class="rounded-lg border border-[#C1C4C9] bg-white px-3 py-1.5 text-xs font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">Xem tuần</a>
                </div>
            </article>
        `).join('');

    if (!contentEl) return;
    contentEl.innerHTML = `
        <div class="space-y-6">
            ${summaryHtml}
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Ca sắp tới</h2>
                <div class="mt-3 grid gap-3 md:grid-cols-2">${listHtml}</div>
            </section>
        </div>
    `;
}

function renderSchedule(): void {
    const today = new Date().toISOString().slice(0, 10);
    const todayAppointments = appointments
        .filter((item) => item.appointment_at.slice(0, 10) === today)
        .sort((a, b) => a.appointment_at.localeCompare(b.appointment_at));

    const waiting = todayAppointments.filter((item) => (item.workflow_status ?? 'awaiting_exam') === 'awaiting_exam');

    const card = (item: Appointment): string => `
        <article class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-bold text-[#333333]">${escapeHtml(item.pet?.name ?? 'Thú cưng chưa rõ')}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">${escapeHtml(item.owner?.name ?? 'Chưa có')} • ${escapeHtml(toLocalDate(item.appointment_at))}</p>
                </div>
                <span class="rounded-full px-2 py-1 text-[11px] font-semibold ${workflowTone(item.workflow_status)}">${escapeHtml(workflowText(item.workflow_status))}</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <button data-accept-case="${item.id}" class="rounded-lg border border-[#0F8A5F] bg-[#0F8A5F] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#0C734F]">Nhận ca</button>
                <select data-workflow-id="${item.id}" class="rounded-lg border border-[#C1C4C9] bg-white px-2 py-1.5 text-xs text-[#333333]">
                    <option value="awaiting_exam" ${(item.workflow_status ?? 'awaiting_exam') === 'awaiting_exam' ? 'selected' : ''}>Chờ khám</option>
                    <option value="examining" ${item.workflow_status === 'examining' ? 'selected' : ''}>Đang khám</option>
                    <option value="awaiting_lab" ${item.workflow_status === 'awaiting_lab' ? 'selected' : ''}>Chờ xét nghiệm</option>
                    <option value="treating" ${item.workflow_status === 'treating' ? 'selected' : ''}>Đang điều trị</option>
                    <option value="completed" ${item.workflow_status === 'completed' ? 'selected' : ''}>Hoàn thành</option>
                    <option value="follow_up" ${item.workflow_status === 'follow_up' ? 'selected' : ''}>Tái khám</option>
                </select>
                <a href="/vet/appointments/${item.id}" class="rounded-lg border border-[#2A6496] bg-[#2A6496] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#235780]">Mở</a>
            </div>
        </article>
    `;

    if (!contentEl) return;
    contentEl.innerHTML = `
        <div class="grid gap-6 xl:grid-cols-2">
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Lịch hẹn hôm nay</h2>
                <div class="mt-3 space-y-3">${todayAppointments.length ? todayAppointments.map(card).join('') : '<div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có lịch hẹn hôm nay.</div>'}</div>
            </section>
            <section>
                <h2 class="text-lg font-bold text-[#333333]">Thú cưng chờ khám</h2>
                <div class="mt-3 space-y-3">${waiting.length ? waiting.map(card).join('') : '<div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Không có ca chờ khám.</div>'}</div>
            </section>
        </div>
    `;

    contentEl.querySelectorAll<HTMLButtonElement>('button[data-accept-case]').forEach((button) => {
        button.addEventListener('click', async () => {
            const id = button.getAttribute('data-accept-case');
            if (!id) return;
            button.disabled = true;
            try {
                await callApi(`/api/vet/appointments/${id}/accept`, 'PATCH');
                await loadBaseData();
                renderBySection();
            } catch {
                button.disabled = false;
            }
        });
    });

    contentEl.querySelectorAll<HTMLSelectElement>('select[data-workflow-id]').forEach((select) => {
        select.addEventListener('change', async () => {
            const id = select.getAttribute('data-workflow-id');
            if (!id) return;
            try {
                await callApi(`/api/vet/appointments/${id}/workflow`, 'PATCH', {
                    workflow_status: select.value,
                });
                await loadBaseData();
                renderBySection();
            } catch {
                // no-op
            }
        });
    });
}

function renderModuleSection(): void {
    const medicalRecord = getCurrentMedicalRecord();

    const sectionMap: Record<string, { title: string; desc: string; fields: string }> = {
        intake: {
            title: 'Tiếp nhận và khám lâm sàng',
            desc: 'Nhập nhiệt độ, cân nặng, nhịp tim, triệu chứng và dấu hiệu bất thường.',
            fields: `
                <div class="grid gap-4 sm:grid-cols-3">
                    <div><label class="text-sm font-semibold text-[#333333]">Nhiệt độ (C)</label><input name="temperature_c" value="${medicalRecord.temperature_c ?? ''}" type="number" step="0.1" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Cân nặng (kg)</label><input name="weight_kg" value="${medicalRecord.weight_kg ?? ''}" type="number" step="0.01" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Nhịp tim (bpm)</label><input name="heart_rate_bpm" value="${medicalRecord.heart_rate_bpm ?? ''}" type="number" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                </div>
                <div><label class="text-sm font-semibold text-[#333333]">Triệu chứng</label><textarea name="symptoms" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.symptoms ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Dấu hiệu bất thường</label><textarea name="abnormal_signs" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.abnormal_signs ?? '')}</textarea></div>
                <input type="hidden" name="workflow_status" value="examining">
            `,
        },
        diagnosis: {
            title: 'Chẩn đoán',
            desc: 'Nhập chẩn đoán sơ bộ, chẩn đoán cuối, bệnh lý và phân mức độ bệnh.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán sơ bộ</label><textarea name="preliminary_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.preliminary_diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán cuối</label><textarea name="final_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.final_diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán chính</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="text-sm font-semibold text-[#333333]">Bệnh lý</label><input name="pathology" value="${escapeHtml(medicalRecord.pathology ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                    <div><label class="text-sm font-semibold text-[#333333]">Mức độ</label><select name="severity_level" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="">Chọn mức độ</option><option value="mild" ${medicalRecord.severity_level === 'mild' ? 'selected' : ''}>Nhẹ</option><option value="moderate" ${medicalRecord.severity_level === 'moderate' ? 'selected' : ''}>Trung bình</option><option value="severe" ${medicalRecord.severity_level === 'severe' ? 'selected' : ''}>Nặng</option><option value="critical" ${medicalRecord.severity_level === 'critical' ? 'selected' : ''}>Nguy kịch</option></select></div>
                </div>
                <input type="hidden" name="workflow_status" value="examining">
            `,
        },
        records: {
            title: 'Lập bệnh án',
            desc: 'Tạo cập nhật bệnh án, phác đồ điều trị, ghi chú theo dõi, tiến triển bệnh.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Phác đồ điều trị</label><textarea name="treatment_protocol" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.treatment_protocol ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Tiến triển bệnh</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Ghi chú</label><textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.notes ?? '')}</textarea></div>
            `,
        },
        orders: {
            title: 'Chỉ định dịch vụ / xét nghiệm',
            desc: 'Nhập danh sách chỉ định theo JSON. Ví dụ: [{"name":"Xét nghiệm máu","status":"ordered","result":null}]',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Chỉ định dịch vụ (JSON)</label><textarea name="service_orders" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.service_orders ?? [], null, 2))}</textarea></div>
                <select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="awaiting_lab" ${(selectedDetail?.data.workflow_status ?? '') === 'awaiting_lab' ? 'selected' : ''}>Chờ xét nghiệm</option><option value="examining">Đang khám</option><option value="treating">Đang điều trị</option></select>
            `,
        },
        prescriptions: {
            title: 'Kê đơn thuốc',
            desc: 'Nhập đơn thuốc tổng quát và danh sách chi tiết JSON.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Đơn thuốc tóm tắt</label><textarea name="prescription" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.treatment ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Đơn thuốc chi tiết (JSON)</label><textarea name="prescriptions" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.prescriptions ?? [], null, 2))}</textarea></div>
                <input type="hidden" name="workflow_status" value="treating">
            `,
        },
        procedures: {
            title: 'Điều trị / thủ thuật',
            desc: 'Ghi nhận thủ thuật và can thiệp điều trị theo JSON.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Thủ thuật (JSON)</label><textarea name="procedures" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.procedures ?? [], null, 2))}</textarea></div>
                <select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="treating" ${(selectedDetail?.data.workflow_status ?? '') === 'treating' ? 'selected' : ''}>Đang điều trị</option><option value="completed">Hoàn thành</option></select>
            `,
        },
        vaccinations: {
            title: 'Quản lý tiêm phòng',
            desc: 'Theo dõi lịch sử tiêm và lập kế hoạch mũi tiếp theo qua kế hoạch tái khám.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Kế hoạch tiêm phòng và mũi sau</label><textarea name="follow_up_plan" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.follow_up_plan ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Ngày tái khám / nhắc mũi</label><input name="follow_up_at" type="date" value="${escapeHtml(selectedDetail?.data.follow_up_at?.slice(0, 10) ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                <input type="hidden" name="workflow_status" value="follow_up">
            `,
        },
        inpatient: {
            title: 'Theo dõi nội trú',
            desc: 'Nhập log theo dõi và điều chỉnh phác đồ cho ca nội trú.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Tiến triển bệnh</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Nhật ký theo dõi (JSON)</label><textarea name="progress_logs" rows="7" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none">${escapeHtml(JSON.stringify(medicalRecord.progress_logs ?? [], null, 2))}</textarea></div>
                <input type="hidden" name="workflow_status" value="treating">
            `,
        },
        follow_up: {
            title: 'Tái khám',
            desc: 'Lên lịch tái khám và cập nhật đánh giá hồi phục.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán</label><textarea name="diagnosis" rows="2" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Đánh giá hồi phục</label><textarea name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.disease_progress ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Kế hoạch tái khám</label><textarea name="follow_up_plan" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.follow_up_plan ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Ngày tái khám</label><input name="follow_up_at" type="date" value="${escapeHtml(selectedDetail?.data.follow_up_at?.slice(0, 10) ?? '')}" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"></div>
                <input type="hidden" name="workflow_status" value="follow_up">
            `,
        },
        sign_off: {
            title: 'Ký xác nhận chuyên môn',
            desc: 'Duyệt bệnh án và kết luận điều trị.',
            fields: `
                <div><label class="text-sm font-semibold text-[#333333]">Chẩn đoán kết luận</label><textarea name="diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none" required>${escapeHtml(medicalRecord.final_diagnosis ?? medicalRecord.diagnosis ?? '')}</textarea></div>
                <div><label class="text-sm font-semibold text-[#333333]">Kết luận điều trị</label><textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none">${escapeHtml(medicalRecord.notes ?? '')}</textarea></div>
                <div class="flex items-center gap-2"><input name="sign_off" value="1" type="checkbox" ${medicalRecord.signed_off_at ? 'checked' : ''} class="h-4 w-4 rounded border-[#C1C4C9]"><label class="text-sm font-semibold text-[#333333]">Ký duyệt hồ sơ</label></div>
                <select name="workflow_status" class="w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none"><option value="completed">Hoàn thành</option><option value="follow_up" ${(selectedDetail?.data.workflow_status ?? '') === 'follow_up' ? 'selected' : ''}>Tái khám</option></select>
            `,
        },
    };

    const section = sectionMap[sectionKey];
    if (!section) {
        renderSchedule();
        return;
    }

    if (!contentEl) return;
    contentEl.innerHTML = sectionFormShell(section.title, section.desc, section.fields);

    const appointmentSelect = document.getElementById('vet-module-appointment') as HTMLSelectElement | null;
    appointmentSelect?.addEventListener('change', async () => {
        selectedAppointmentId = appointmentSelect.value ? Number(appointmentSelect.value) : null;
        await loadSelectedDetail();
        renderBySection();
    });

    const refreshButton = document.getElementById('vet-module-refresh') as HTMLButtonElement | null;
    refreshButton?.addEventListener('click', async () => {
        await loadBaseData();
        await loadSelectedDetail();
        renderBySection();
    });

    const acceptButton = document.getElementById('vet-module-accept') as HTMLButtonElement | null;
    acceptButton?.addEventListener('click', async () => {
        if (!selectedAppointmentId) return;

        acceptButton.disabled = true;
        try {
            await callApi(`/api/vet/appointments/${selectedAppointmentId}/accept`, 'PATCH');
            await loadBaseData();
            await loadSelectedDetail();
            renderBySection();
            setModuleStatus('Đã nhận ca khám.', 'success');
        } catch (error) {
            setModuleStatus((error as Error).message || 'Không thể nhận ca khám.', 'error');
            acceptButton.disabled = false;
        }
    });

    const printButton = document.getElementById('vet-module-print') as HTMLButtonElement | null;
    printButton?.addEventListener('click', () => {
        if (!selectedAppointmentId) {
            setModuleStatus('Vui lòng chọn một lịch hẹn trước.', 'error');
            return;
        }
        window.open(`/api/vet/appointments/${selectedAppointmentId}/prescription/pdf`, '_blank');
    });

    const contextEl = document.getElementById('vet-module-context');
    if (contextEl) {
        contextEl.innerHTML = buildContextHtml();
    }

    const formEl = document.getElementById('vet-module-form') as HTMLFormElement | null;
    formEl?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!selectedAppointmentId) {
            setModuleStatus('Vui lòng chọn một lịch hẹn trước.', 'error');
            return;
        }

        const formData = new FormData(formEl);
        const diagnosis = String(formData.get('diagnosis') ?? '').trim();

        if (!diagnosis) {
            setModuleStatus('Cần nhập chẩn đoán.', 'error');
            return;
        }

        try {
            const payload: Record<string, unknown> = {
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

            await callApi(`/api/vet/appointments/${selectedAppointmentId}/medical-record`, 'PUT', payload);

            await loadBaseData();
            await loadSelectedDetail();
            renderBySection();
            setModuleStatus('Đã lưu thành công.', 'success');
        } catch (error) {
            setModuleStatus((error as Error).message || 'Không thể lưu.', 'error');
        }
    });
}

async function loadBaseData(): Promise<void> {
    const [appointmentsResponse, summaryResponse] = await Promise.all([
        callApi<{ data: Appointment[] }>('/api/vet/appointments', 'GET'),
        callApi<{ data: DashboardSummary }>('/api/vet/dashboard-summary', 'GET').catch(() => ({ data: null as unknown as DashboardSummary })),
    ]);

    appointments = appointmentsResponse.data ?? [];
    summary = summaryResponse.data ?? null;

    if (!selectedAppointmentId && appointments.length > 0) {
        selectedAppointmentId = appointments[0].id;
    }
}

async function loadSelectedDetail(): Promise<void> {
    if (!selectedAppointmentId) {
        selectedDetail = null;
        return;
    }

    selectedDetail = await callApi<AppointmentDetailResponse>(`/api/vet/appointments/${selectedAppointmentId}`, 'GET');
}

function renderBySection(): void {
    if (sectionKey === 'dashboard') {
        renderDashboard();
        return;
    }

    if (sectionKey === 'schedule') {
        renderSchedule();
        return;
    }

    renderModuleSection();
}

onMounted(() => {
    if (!contentEl) return;

    contentEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#4A4A4A]">Đang tải mô-đun...</div>';

    void (async () => {
        try {
            await loadBaseData();
            await loadSelectedDetail();
            renderBySection();
        } catch (error) {
            contentEl.innerHTML = `<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-sm text-[#B91C1C]">${escapeHtml((error as Error).message || 'Không thể tải mô-đun.')}</div>`;
        }
    })();
});
</script>

<template>
    <div class="hidden"></div>
</template>
