<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Appointment = {
    id: number;
    appointment_at: string;
    status: string;
    workflow_status?: string;
    queue_number?: number | null;
    reason?: string | null;
    pet?: { name?: string; species?: { name?: string } } | null;
    owner?: { name?: string; phone?: string | null } | null;
    service?: { name?: string } | null;
    medical_record?: { id: number } | null;
};

const dateFilterEl = document.getElementById('vet-date-filter') as HTMLInputElement | null;
const statusFilterEl = document.getElementById('vet-status-filter') as HTMLSelectElement | null;
const listEl = document.getElementById('vet-appointments-list');
const todayCountEl = document.getElementById('vet-today-count');
const confirmedCountEl = document.getElementById('vet-confirmed-count');
const completedCountEl = document.getElementById('vet-completed-count');

function toLocalDateString(value: Date): string {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

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

function formatStatus(status: string): string {
    return status ? status.toUpperCase() : 'N/A';
}

function formatWorkflowStatus(status?: string): string {
    if (!status) return 'Chờ khám';

    const map: Record<string, string> = {
        awaiting_exam: 'Chờ khám',
        examining: 'Đang khám',
        awaiting_lab: 'Chờ xét nghiệm',
        treating: 'Đang điều trị',
        completed: 'Hoàn thành',
        follow_up: 'Tái khám',
    };

    return map[status] ?? status;
}

function statusTone(status: string): string {
    if (status === 'completed') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'follow_up') return 'bg-[#EFF6FF] text-[#1D4ED8]';
    if (status === 'treating') return 'bg-[#EDE9FE] text-[#5B21B6]';
    if (status === 'awaiting_lab') return 'bg-[#FFF7ED] text-[#C2410C]';
    if (status === 'examining') return 'bg-[#FEF3C7] text-[#92400E]';
    if (status === 'awaiting_exam' || status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

function updateStats(appointments: Appointment[]): void {
    const today = toLocalDateString(new Date());
    if (todayCountEl) {
        todayCountEl.textContent = String(
            appointments.filter((item) => item.appointment_at?.slice(0, 10) === today).length,
        );
    }
    if (confirmedCountEl) {
        confirmedCountEl.textContent = String(
            appointments.filter((item) => (item.workflow_status ?? 'awaiting_exam') === 'awaiting_exam').length,
        );
    }
    if (completedCountEl) {
        completedCountEl.textContent = String(
            appointments.filter((item) => item.status === 'completed').length,
        );
    }
}

function renderAppointments(appointments: Appointment[]): void {
    if (!listEl) return;

    if (appointments.length === 0) {
        listEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">Không có lịch hẹn phù hợp bộ lọc này.</div>';
        return;
    }

    listEl.innerHTML = appointments.map((appointment) => `
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.03)]">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-base font-bold text-[#333333]">${appointment.pet?.name ?? 'Thú cưng chưa rõ'}${appointment.pet?.species?.name ? ` • ${appointment.pet.species.name}` : ''}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Chủ nuôi: ${appointment.owner?.name ?? 'Chưa có'}${appointment.owner?.phone ? ` • ${appointment.owner.phone}` : ''}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Thời gian: ${formatDateTime(appointment.appointment_at)}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${statusTone(appointment.workflow_status ?? appointment.status)}">${formatWorkflowStatus(appointment.workflow_status)}</span>
                    <span class="text-xs text-[#64748B]">${appointment.medical_record ? 'Đã lưu bệnh án' : 'Chờ bệnh án'}</span>
                </div>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-3 text-sm text-[#4A4A4A]">
                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Số thứ tự:</span> ${appointment.queue_number ?? 'Chưa có'}</div>
                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Dịch vụ:</span> ${appointment.service?.name ?? 'Chưa có'}</div>
                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-3"><span class="font-semibold text-[#333333]">Lý do:</span> ${appointment.reason ?? 'Chưa có'}</div>
            </div>
            <div class="mt-4">
                ${(appointment.workflow_status ?? 'awaiting_exam') === 'awaiting_exam' ? `<button data-accept-id="${appointment.id}" class="mr-2 inline-flex items-center rounded-xl border border-[#0F8A5F] bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0C734F]">Nhận ca khám</button>` : ''}
                <a href="/vet/appointments/${appointment.id}" class="inline-flex items-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#235780]">
                    Mở ca khám
                </a>
            </div>
        </article>
    `).join('');

    listEl.querySelectorAll<HTMLButtonElement>('button[data-accept-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            const id = button.getAttribute('data-accept-id');
            if (!id) return;

            button.disabled = true;

            try {
                await callApi(`/api/vet/appointments/${id}/accept`, 'PATCH');
                await loadAppointments();
            } catch {
                button.disabled = false;
            }
        });
    });
}

async function loadAppointments(): Promise<void> {
    const params = new URLSearchParams();
    if (dateFilterEl?.value) params.set('date', dateFilterEl.value);
    if (statusFilterEl?.value) params.set('workflow_status', statusFilterEl.value);

    const url = params.size > 0 ? `/api/vet/appointments?${params.toString()}` : '/api/vet/appointments';
    const response = await callApi<{ data: Appointment[] }>(url, 'GET');
    updateStats(response.data);
    renderAppointments(response.data);
}

onMounted(() => {
    loadAppointments().catch(() => {
        if (listEl) {
            listEl.innerHTML = '<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-5 text-[#B91C1C]">Không thể tải danh sách lịch hẹn.</div>';
        }
    });

    dateFilterEl?.addEventListener('change', () => {
        void loadAppointments();
    });

    statusFilterEl?.addEventListener('change', () => {
        void loadAppointments();
    });
});
</script>

<template>
    <div class="hidden"></div>
</template>
