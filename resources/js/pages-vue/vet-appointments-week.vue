<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Appointment = {
    id: number;
    appointment_at: string;
    status: string;
    queue_number?: number | null;
    reason?: string | null;
    pet?: { name?: string; species?: { name?: string } } | null;
    owner?: { name?: string; phone?: string | null } | null;
};

const gridEl = document.getElementById('vet-week-grid');
const rangeEl = document.getElementById('vet-week-range');
const totalEl = document.getElementById('vet-week-total');
const completedEl = document.getElementById('vet-week-completed');

const prevButtonEl = document.getElementById('vet-week-prev') as HTMLButtonElement | null;
const currentButtonEl = document.getElementById('vet-week-current') as HTMLButtonElement | null;
const nextButtonEl = document.getElementById('vet-week-next') as HTMLButtonElement | null;

let allAppointments: Appointment[] = [];
let weekAnchor = new Date();

function toLocalDateString(value: Date): string {
    const year = value.getFullYear();
    const month = String(value.getMonth() + 1).padStart(2, '0');
    const day = String(value.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function startOfWeek(date: Date): Date {
    const copy = new Date(date);
    copy.setHours(0, 0, 0, 0);

    const day = copy.getDay();
    const offset = day === 0 ? -6 : 1 - day;
    copy.setDate(copy.getDate() + offset);

    return copy;
}

function addDays(date: Date, days: number): Date {
    const copy = new Date(date);
    copy.setDate(copy.getDate() + days);
    return copy;
}

function formatDateLabel(date: Date): string {
    return date.toLocaleDateString('vi-VN', {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
    });
}

function formatDateTime(input: string): string {
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;

    return date.toLocaleString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
    });
}

function statusBadge(status: string): string {
    if (status === 'completed') {
        return 'bg-[#ECFDF3] text-[#027A48]';
    }

    if (status === 'confirmed') {
        return 'bg-[#FFFBEB] text-[#B45309]';
    }

    if (status === 'cancelled') {
        return 'bg-[#FEF2F2] text-[#B91C1C]';
    }

    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

function renderWeek(): void {
    if (!gridEl) return;

    const weekStart = startOfWeek(weekAnchor);
    const weekDates = Array.from({ length: 7 }, (_, index) => addDays(weekStart, index));

    const grouped: Record<string, Appointment[]> = {};
    for (const date of weekDates) {
        grouped[toLocalDateString(date)] = [];
    }

    for (const appointment of allAppointments) {
        const key = appointment.appointment_at?.slice(0, 10);
        if (key && grouped[key]) {
            grouped[key].push(appointment);
        }
    }

    for (const key of Object.keys(grouped)) {
        grouped[key].sort((left, right) => left.appointment_at.localeCompare(right.appointment_at));
    }

    const weekTotal = Object.values(grouped).reduce((sum, items) => sum + items.length, 0);
    const completedTotal = Object.values(grouped)
        .flat()
        .filter((item) => item.status === 'completed')
        .length;

    if (rangeEl) {
        const end = addDays(weekStart, 6);
        rangeEl.textContent = `${toLocalDateString(weekStart)} - ${toLocalDateString(end)}`;
    }

    if (totalEl) {
        totalEl.textContent = String(weekTotal);
    }

    if (completedEl) {
        completedEl.textContent = String(completedTotal);
    }

    gridEl.innerHTML = weekDates.map((date) => {
        const key = toLocalDateString(date);
        const items = grouped[key] ?? [];

        const cards = items.length === 0
            ? '<p class="rounded-xl border border-dashed border-[#C7CFDA] bg-white px-3 py-3 text-xs text-[#64748B]">No appointments</p>'
            : items.map((appointment) => `
                <article class="rounded-xl border border-[#DDE1E6] bg-white p-3">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-bold text-[#333333]">${appointment.pet?.name ?? 'Unknown pet'}</p>
                        <span class="rounded-full px-2 py-1 text-[10px] font-semibold ${statusBadge(appointment.status)}">${appointment.status.toUpperCase()}</span>
                    </div>
                    <p class="mt-1 text-xs text-[#4A4A4A]">${appointment.pet?.species?.name ?? 'N/A'} • ${appointment.owner?.name ?? 'N/A'}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">${formatDateTime(appointment.appointment_at)}</p>
                    <p class="mt-1 text-xs text-[#64748B]">Queue: ${appointment.queue_number ?? 'N/A'}</p>
                    <a href="/vet/appointments/${appointment.id}" class="mt-2 inline-flex items-center rounded-lg border border-[#2A6496] bg-[#2A6496] px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-[#235780]">Open</a>
                </article>
            `).join('');

        return `
            <section class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-3">
                <header class="mb-3 rounded-xl bg-[#EAF2FB] px-3 py-2 text-xs font-bold uppercase tracking-[0.08em] text-[#2A6496]">${formatDateLabel(date)}</header>
                <div class="space-y-2">${cards}</div>
            </section>
        `;
    }).join('');
}

async function loadAppointments(): Promise<void> {
    if (gridEl) {
        gridEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Loading weekly schedule...</div>';
    }

    const response = await callApi<{ data: Appointment[] }>('/api/vet/appointments', 'GET');
    allAppointments = response.data ?? [];
    renderWeek();
}

onMounted(() => {
    void loadAppointments().catch(() => {
        if (gridEl) {
            gridEl.innerHTML = '<div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] p-4 text-sm text-[#B91C1C]">Failed to load weekly schedule.</div>';
        }
    });

    prevButtonEl?.addEventListener('click', () => {
        weekAnchor = addDays(weekAnchor, -7);
        renderWeek();
    });

    currentButtonEl?.addEventListener('click', () => {
        weekAnchor = new Date();
        renderWeek();
    });

    nextButtonEl?.addEventListener('click', () => {
        weekAnchor = addDays(weekAnchor, 7);
        renderWeek();
    });
});
</script>

<template>
    <div class="hidden"></div>
</template>
