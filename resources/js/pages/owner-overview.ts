import { callApi } from '../auth/http';

type Pet = {
    id: number;
    name: string;
};

type Appointment = {
    id: number;
    appointment_at: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    pet?: { id: number; name: string } | null;
};

const petsCountEl = document.getElementById('overview-pets-count');
const appointmentsCountEl = document.getElementById('overview-appointments-count');
const pendingCountEl = document.getElementById('overview-pending-count');
const recentListEl = document.getElementById('overview-recent-appointments');

function formatDateTime(input: string): string {
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString();
}

function setText(el: HTMLElement | null, value: string): void {
    if (!el) return;
    el.textContent = value;
}

function renderRecentAppointments(items: Appointment[]): void {
    if (!recentListEl) return;

    if (items.length === 0) {
        recentListEl.innerHTML = '<p>No appointments yet.</p>';
        return;
    }

    recentListEl.innerHTML = items
        .slice(0, 5)
        .map((item) => {
            const petName = item.pet?.name ?? 'Unknown pet';
            return `<div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2"><p class="font-semibold text-[#333333]">${petName}</p><p class="text-xs text-[#4A4A4A]">${formatDateTime(item.appointment_at)} | ${item.status}</p></div>`;
        })
        .join('');
}

async function bootstrap(): Promise<void> {
    try {
        const [petsResponse, appointmentsResponse] = await Promise.all([
            callApi<{ data: Pet[] }>('/api/owner/pets', 'GET'),
            callApi<{ data: Appointment[] }>('/api/owner/appointments', 'GET'),
        ]);

        const pets = petsResponse.data;
        const appointments = appointmentsResponse.data;

        setText(petsCountEl, String(pets.length));
        setText(appointmentsCountEl, String(appointments.length));
        setText(
            pendingCountEl,
            String(appointments.filter((item) => item.status === 'pending').length),
        );

        renderRecentAppointments(appointments);
    } catch (error) {
        setText(recentListEl, (error as Error).message);
    }
}

bootstrap();
