<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type MedicalRecord = {
    id?: number;
    record_code?: string | null;
    temperature_c?: number | string | null;
    weight_kg?: number | string | null;
    heart_rate_bpm?: number | null;
    symptoms?: string | null;
    abnormal_signs?: string | null;
    preliminary_diagnosis?: string | null;
    diagnosis?: string | null;
    final_diagnosis?: string | null;
    pathology?: string | null;
    severity_level?: string | null;
    prescription?: string | null;
    treatment?: string | null;
    treatment_protocol?: string | null;
    disease_progress?: string | null;
    follow_up_plan?: string | null;
    service_orders?: Array<Record<string, unknown>> | null;
    prescriptions?: Array<Record<string, unknown>> | null;
    procedures?: Array<Record<string, unknown>> | null;
    progress_logs?: Array<Record<string, unknown>> | null;
    signed_off_at?: string | null;
    notes?: string | null;
    record_date?: string | null;
};

type Vaccination = {
    id: number;
    vaccine_name?: string | null;
    vaccinated_on?: string | null;
    next_due_on?: string | null;
};

type AppointmentDetail = {
    id: number;
    appointment_at: string;
    status: string;
    workflow_status?: string | null;
    follow_up_at?: string | null;
    queue_number?: number | null;
    reason?: string | null;
    pet?: { name?: string; breed?: string | null; species?: { name?: string } } | null;
    owner?: { name?: string; phone?: string | null; email?: string | null } | null;
    doctor?: { user?: { name?: string } } | null;
    service?: { name?: string; price?: number | string } | null;
    medical_record?: MedicalRecord | null;
};

type ShowResponse = {
    data: AppointmentDetail;
    meta?: {
        vaccination_history?: Vaccination[];
        previous_medical_records?: MedicalRecord[];
    };
};

const rootEl = document.getElementById('vet-appointment-root');
const contentEl = document.getElementById('vet-appointment-content');
const statusEl = document.getElementById('vet-record-status');
const formEl = document.getElementById('vet-medical-record-form') as HTMLFormElement | null;
const submitButtonEl = document.getElementById('vet-record-submit') as HTMLButtonElement | null;

const recordDateEl = document.getElementById('record-date') as HTMLInputElement | null;
const temperatureEl = document.getElementById('record-temperature') as HTMLInputElement | null;
const weightEl = document.getElementById('record-weight') as HTMLInputElement | null;
const heartRateEl = document.getElementById('record-heart-rate') as HTMLInputElement | null;
const symptomsEl = document.getElementById('record-symptoms') as HTMLTextAreaElement | null;
const abnormalSignsEl = document.getElementById('record-abnormal-signs') as HTMLTextAreaElement | null;
const preliminaryDiagnosisEl = document.getElementById('record-preliminary-diagnosis') as HTMLTextAreaElement | null;
const diagnosisEl = document.getElementById('record-diagnosis') as HTMLTextAreaElement | null;
const finalDiagnosisEl = document.getElementById('record-final-diagnosis') as HTMLTextAreaElement | null;
const pathologyEl = document.getElementById('record-pathology') as HTMLInputElement | null;
const severityEl = document.getElementById('record-severity') as HTMLSelectElement | null;
const prescriptionEl = document.getElementById('record-prescription') as HTMLTextAreaElement | null;
const treatmentProtocolEl = document.getElementById('record-treatment-protocol') as HTMLTextAreaElement | null;
const diseaseProgressEl = document.getElementById('record-disease-progress') as HTMLTextAreaElement | null;
const followUpPlanEl = document.getElementById('record-follow-up-plan') as HTMLTextAreaElement | null;
const workflowStatusEl = document.getElementById('record-workflow-status') as HTMLSelectElement | null;
const followUpAtEl = document.getElementById('record-follow-up-at') as HTMLInputElement | null;
const serviceOrdersEl = document.getElementById('record-service-orders') as HTMLTextAreaElement | null;
const prescriptionsEl = document.getElementById('record-prescriptions-list') as HTMLTextAreaElement | null;
const proceduresEl = document.getElementById('record-procedures') as HTMLTextAreaElement | null;
const progressLogsEl = document.getElementById('record-progress-logs') as HTMLTextAreaElement | null;
const signOffEl = document.getElementById('record-sign-off') as HTMLInputElement | null;
const notesEl = document.getElementById('record-notes') as HTMLTextAreaElement | null;

let currentData: ShowResponse | null = null;

function formatDateTime(input: string): string {
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

function setStatus(message: string, tone: 'success' | 'error'): void {
    if (!statusEl) return;
    statusEl.textContent = message;
    statusEl.className = `mb-4 rounded-2xl px-4 py-3 text-sm ${tone === 'success' ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF2F2] text-[#B91C1C]'}`;
    statusEl.classList.remove('hidden');
}

function workflowLabel(status?: string | null): string {
    const map: Record<string, string> = {
        awaiting_exam: 'Cho kham',
        examining: 'Dang kham',
        awaiting_lab: 'Cho xet nghiem',
        treating: 'Dang dieu tri',
        completed: 'Hoan thanh',
        follow_up: 'Tai kham',
    };

    if (!status) return 'Cho kham';
    return map[status] ?? status;
}

function stringifyJson(value: unknown): string {
    if (!value) return '';
    return JSON.stringify(value, null, 2);
}

function parseJsonArray(value: string): Array<Record<string, unknown>> | null {
    const trimmed = value.trim();
    if (!trimmed) return null;

    const parsed = JSON.parse(trimmed);
    if (!Array.isArray(parsed)) {
        throw new Error('JSON payload must be an array.');
    }

    return parsed as Array<Record<string, unknown>>;
}

function renderAppointment(payload: ShowResponse): void {
    if (!contentEl) return;

    const appointment = payload.data;
    const vaccinations = payload.meta?.vaccination_history ?? [];
    const previousRecords = payload.meta?.previous_medical_records ?? [];

    const vaccinationHtml = vaccinations.length === 0
        ? '<p class="text-xs text-[#64748B]">Chua co lich su tiem phong.</p>'
        : vaccinations.map((item) => `<li class="text-xs text-[#4A4A4A]">${item.vaccine_name ?? 'Vaccine'} • ${item.vaccinated_on ?? 'N/A'}${item.next_due_on ? ` • Mui sau: ${item.next_due_on}` : ''}</li>`).join('');

    const previousRecordHtml = previousRecords.length === 0
        ? '<p class="text-xs text-[#64748B]">Chua co lan kham truoc.</p>'
        : previousRecords.map((record) => `<li class="text-xs text-[#4A4A4A]">${record.record_code ?? 'N/A'} • ${record.record_date ?? 'N/A'} • ${record.final_diagnosis ?? record.diagnosis ?? 'N/A'}</li>`).join('');

    const acceptButton = (appointment.workflow_status ?? 'awaiting_exam') === 'awaiting_exam'
        ? '<button id="vet-accept-case" type="button" class="inline-flex items-center rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F]">Nhan ca kham</button>'
        : '';

    contentEl.innerHTML = `
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Pet:</span> ${appointment.pet?.name ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Species:</span> ${appointment.pet?.species?.name ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Breed:</span> ${appointment.pet?.breed ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Owner:</span> ${appointment.owner?.name ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Phone:</span> ${appointment.owner?.phone ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Email:</span> ${appointment.owner?.email ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Doctor:</span> ${appointment.doctor?.user?.name ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Service:</span> ${appointment.service?.name ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Date time:</span> ${formatDateTime(appointment.appointment_at)}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Status:</span> ${workflowLabel(appointment.workflow_status)}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Reason:</span> ${appointment.reason ?? 'N/A'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Medical Record Code:</span> ${appointment.medical_record?.record_code ?? 'Chua tao'}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Vaccination History:</span><ul class="mt-2 space-y-1">${vaccinationHtml}</ul></div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Previous Visits:</span><ul class="mt-2 space-y-1">${previousRecordHtml}</ul></div>
            ${acceptButton ? `<div class="sm:col-span-2">${acceptButton}</div>` : ''}
        </div>
    `;

    const acceptEl = document.getElementById('vet-accept-case') as HTMLButtonElement | null;
    acceptEl?.addEventListener('click', async () => {
        if (!rootEl) return;

        const appointmentId = Number(rootEl.getAttribute('data-appointment-id'));
        if (!appointmentId) return;

        acceptEl.disabled = true;
        try {
            await callApi(`/api/vet/appointments/${appointmentId}/accept`, 'PATCH');
            await loadAppointment();
            setStatus('Da tiep nhan ca kham.', 'success');
        } catch (error) {
            setStatus((error as Error).message || 'Khong the tiep nhan ca kham.', 'error');
            acceptEl.disabled = false;
        }
    });
}

function fillRecordForm(record?: MedicalRecord | null): void {
    const today = new Date().toISOString().slice(0, 10);
    if (recordDateEl) recordDateEl.value = record?.record_date ?? today;
    if (temperatureEl) temperatureEl.value = String(record?.temperature_c ?? '');
    if (weightEl) weightEl.value = String(record?.weight_kg ?? '');
    if (heartRateEl) heartRateEl.value = String(record?.heart_rate_bpm ?? '');
    if (symptomsEl) symptomsEl.value = record?.symptoms ?? '';
    if (abnormalSignsEl) abnormalSignsEl.value = record?.abnormal_signs ?? '';
    if (preliminaryDiagnosisEl) preliminaryDiagnosisEl.value = record?.preliminary_diagnosis ?? '';
    if (diagnosisEl) diagnosisEl.value = record?.diagnosis ?? '';
    if (finalDiagnosisEl) finalDiagnosisEl.value = record?.final_diagnosis ?? '';
    if (pathologyEl) pathologyEl.value = record?.pathology ?? '';
    if (severityEl) severityEl.value = record?.severity_level ?? '';
    if (prescriptionEl) prescriptionEl.value = record?.prescription ?? record?.treatment ?? '';
    if (treatmentProtocolEl) treatmentProtocolEl.value = record?.treatment_protocol ?? '';
    if (diseaseProgressEl) diseaseProgressEl.value = record?.disease_progress ?? '';
    if (followUpPlanEl) followUpPlanEl.value = record?.follow_up_plan ?? '';
    if (serviceOrdersEl) serviceOrdersEl.value = stringifyJson(record?.service_orders);
    if (prescriptionsEl) prescriptionsEl.value = stringifyJson(record?.prescriptions);
    if (proceduresEl) proceduresEl.value = stringifyJson(record?.procedures);
    if (progressLogsEl) progressLogsEl.value = stringifyJson(record?.progress_logs);
    if (signOffEl) signOffEl.checked = Boolean(record?.signed_off_at);
    if (notesEl) notesEl.value = record?.notes ?? '';

    if (workflowStatusEl) {
        workflowStatusEl.value = currentData?.data?.workflow_status ?? 'awaiting_exam';
    }

    if (followUpAtEl) {
        followUpAtEl.value = currentData?.data?.follow_up_at?.slice(0, 10) ?? '';
    }
}

async function loadAppointment(): Promise<void> {
    if (!rootEl || !contentEl) return;

    const appointmentId = Number(rootEl.getAttribute('data-appointment-id'));
    if (!appointmentId) {
        contentEl.innerHTML = '<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-[#B91C1C]">Invalid appointment id.</div>';
        return;
    }

    const response = await callApi<ShowResponse>(`/api/vet/appointments/${appointmentId}`, 'GET');
    currentData = response;
    renderAppointment(response);
    fillRecordForm(response.data.medical_record);
}

async function saveMedicalRecord(): Promise<void> {
    if (!rootEl) return;

    const appointmentId = Number(rootEl.getAttribute('data-appointment-id'));
    if (!appointmentId) return;

    submitButtonEl?.setAttribute('disabled', 'true');

    try {
        const serviceOrders = parseJsonArray(serviceOrdersEl?.value || '');
        const prescriptions = parseJsonArray(prescriptionsEl?.value || '');
        const procedures = parseJsonArray(proceduresEl?.value || '');
        const progressLogs = parseJsonArray(progressLogsEl?.value || '');

        await callApi(`/api/vet/appointments/${appointmentId}/medical-record`, 'PUT', {
            record_date: recordDateEl?.value || null,
            temperature_c: temperatureEl?.value ? Number(temperatureEl.value) : null,
            weight_kg: weightEl?.value ? Number(weightEl.value) : null,
            heart_rate_bpm: heartRateEl?.value ? Number(heartRateEl.value) : null,
            symptoms: symptomsEl?.value || null,
            abnormal_signs: abnormalSignsEl?.value || null,
            preliminary_diagnosis: preliminaryDiagnosisEl?.value || null,
            diagnosis: diagnosisEl?.value || '',
            final_diagnosis: finalDiagnosisEl?.value || null,
            pathology: pathologyEl?.value || null,
            severity_level: severityEl?.value || null,
            prescription: prescriptionEl?.value || '',
            treatment_protocol: treatmentProtocolEl?.value || null,
            disease_progress: diseaseProgressEl?.value || null,
            follow_up_plan: followUpPlanEl?.value || null,
            service_orders: serviceOrders,
            prescriptions,
            procedures,
            progress_logs: progressLogs,
            workflow_status: workflowStatusEl?.value || 'completed',
            follow_up_at: followUpAtEl?.value || null,
            sign_off: Boolean(signOffEl?.checked),
            notes: notesEl?.value || null,
        });

        setStatus('Medical record saved successfully.', 'success');
        await loadAppointment();
    } catch (error) {
        setStatus((error as Error).message || 'Failed to save medical record.', 'error');
    } finally {
        submitButtonEl?.removeAttribute('disabled');
    }
}

onMounted(() => {
    loadAppointment().catch(() => {
        if (contentEl) {
            contentEl.innerHTML = '<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-[#B91C1C]">Failed to load appointment detail.</div>';
        }
    });

    formEl?.addEventListener('submit', (event) => {
        event.preventDefault();
        void saveMedicalRecord();
    });
});
</script>

<template>
    <div class="hidden"></div>
</template>
