<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type AppointmentDetail = {
    id: number;
    appointment_at: string;
    status: string;
    queue_number?: number | null;
    reason?: string | null;
    is_emergency?: boolean;
    pet?: { name?: string; species?: { name?: string } };
    owner?: { name?: string; phone?: string; email?: string };
    doctor?: { user?: { name?: string } };
    service?: { name?: string; price?: number };
};

async function loadAppointmentDetail() {
    const root = document.getElementById('appointment-detail-root');
    const content = document.getElementById('appointment-detail-content');

    if (!root || !content) return;

    const id = Number(root.getAttribute('data-appointment-id'));
    if (!id) {
        content.innerHTML = '<div class="rounded-xl bg-red-50 p-4 text-center text-red-600">Invalid appointment id.</div>';
        return;
    }

    try {
        const res = await callApi<{ data: AppointmentDetail }>(`/api/receptionist/appointments/${id}`, 'GET');
        const app = res?.data;

        if (!app) {
            content.innerHTML = '<div class="rounded-xl bg-red-50 p-4 text-center text-red-600">Appointment not found.</div>';
            return;
        }

        content.innerHTML = `
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Pet:</span> ${app.pet?.name || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Species:</span> ${app.pet?.species?.name || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Owner:</span> ${app.owner?.name || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Phone:</span> ${app.owner?.phone || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Email:</span> ${app.owner?.email || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Date time:</span> ${formatDateTime(app.appointment_at)}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Status:</span> ${app.status || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Queue number:</span> ${app.queue_number ? String(app.queue_number) : 'Not in queue yet'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Doctor:</span> ${app.doctor?.user?.name || 'Unassigned'}</div>
                <div class="rounded-xl bg-gray-50 p-4"><span class="font-semibold text-gray-900">Service:</span> ${app.service?.name || 'N/A'}</div>
                <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2"><span class="font-semibold text-gray-900">Reason:</span> ${app.reason || 'N/A'}</div>
            </div>
        `;
    } catch (e) {
        content.innerHTML = '<div class="rounded-xl bg-red-50 p-4 text-center text-red-600">Failed to load appointment details.</div>';
    }
}

function formatDateTime(dateTime: string): string {
    if (!dateTime) return 'N/A';

    const date = new Date(dateTime);
    if (Number.isNaN(date.getTime())) return dateTime;

    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

onMounted(() => {
    loadAppointmentDetail();
});
</script>

<template>
    <div class="hidden"></div>
</template>
