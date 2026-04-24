<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type MedicalRecord = {
    id?: number;
    symptoms?: string | null;
    diagnosis?: string | null;
    treatment?: string | null;
    notes?: string | null;
    record_date?: string | null;
};

type AppointmentDetail = {
    id: number;
    appointment_at: string;
    status: string;
    queue_number?: number | null;
    reason?: string | null;
    pet?: { name?: string; breed?: string | null; species?: { name?: string } } | null;
    owner?: { name?: string; phone?: string | null; email?: string | null } | null;
    doctor?: { user?: { name?: string } } | null;
    service?: { name?: string; price?: number | string } | null;
    medical_record?: MedicalRecord | null;
};

const rootEl = document.getElementById('vet-appointment-root');
const contentEl = document.getElementById('vet-appointment-content');
const statusEl = document.getElementById('vet-record-status');
const formEl = document.getElementById('vet-medical-record-form') as HTMLFormElement | null;
const submitButtonEl = document.getElementById('vet-record-submit') as HTMLButtonElement | null;

const recordDateEl = document.getElementById('record-date') as HTMLInputElement | null;
const symptomsEl = document.getElementById('record-symptoms') as HTMLTextAreaElement | null;
const diagnosisEl = document.getElementById('record-diagnosis') as HTMLTextAreaElement | null;
const treatmentEl = document.getElementById('record-treatment') as HTMLTextAreaElement | null;
const notesEl = document.getElementById('record-notes') as HTMLTextAreaElement | null;

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

function renderAppointment(appointment: AppointmentDetail): void {
    if (!contentEl) return;

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
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4"><span class="font-semibold text-[#333333]">Status:</span> ${appointment.status.toUpperCase()}</div>
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-4 sm:col-span-2"><span class="font-semibold text-[#333333]">Reason:</span> ${appointment.reason ?? 'N/A'}</div>
        </div>
    `;
}

function fillRecordForm(record?: MedicalRecord | null): void {
    const today = new Date().toISOString().slice(0, 10);
    if (recordDateEl) recordDateEl.value = record?.record_date ?? today;
    if (symptomsEl) symptomsEl.value = record?.symptoms ?? '';
    if (diagnosisEl) diagnosisEl.value = record?.diagnosis ?? '';
    if (treatmentEl) treatmentEl.value = record?.treatment ?? '';
    if (notesEl) notesEl.value = record?.notes ?? '';
}

async function loadAppointment(): Promise<void> {
    if (!rootEl || !contentEl) return;

    const appointmentId = Number(rootEl.getAttribute('data-appointment-id'));
    if (!appointmentId) {
        contentEl.innerHTML = '<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-[#B91C1C]">Invalid appointment id.</div>';
        return;
    }

    const response = await callApi<{ data: AppointmentDetail }>(`/api/vet/appointments/${appointmentId}`, 'GET');
    renderAppointment(response.data);
    fillRecordForm(response.data.medical_record);
}

async function saveMedicalRecord(): Promise<void> {
    if (!rootEl) return;

    const appointmentId = Number(rootEl.getAttribute('data-appointment-id'));
    if (!appointmentId) return;

    submitButtonEl?.setAttribute('disabled', 'true');

    try {
        await callApi(`/api/vet/appointments/${appointmentId}/medical-record`, 'PUT', {
            record_date: recordDateEl?.value || null,
            symptoms: symptomsEl?.value || null,
            diagnosis: diagnosisEl?.value || '',
            treatment: treatmentEl?.value || '',
            notes: notesEl?.value || null,
        });

        setStatus('Medical record saved and appointment marked as completed.', 'success');
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
