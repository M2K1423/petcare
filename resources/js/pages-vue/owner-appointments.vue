<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Pet = {
    id: number;
    name: string;
    species?: { id: number; name: string } | null;
};

type Appointment = {
    id: number;
    appointment_at: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    reason: string | null;
    pet?: {
        id: number;
        name: string;
        species?: { id: number; name: string } | null;
    } | null;
};

type AuthMeResponse = {
    authenticated?: boolean;
    user?: { role?: string | null } | null;
};

const statusEl = document.getElementById('owner-appointments-status');
const petSelectEl = document.getElementById('appointment-pet-select') as HTMLSelectElement | null;
const appointmentForm = document.getElementById('owner-appointment-form') as HTMLFormElement | null;
const appointmentsListEl = document.getElementById('owner-appointments-list');

function setStatus(message: string, kind: 'neutral' | 'success' | 'error' = 'neutral'): void {
    if (!statusEl) return;

    const classMap = {
        neutral: 'mt-2 text-sm text-[#4A4A4A]',
        success: 'mt-2 text-sm text-emerald-700',
        error: 'mt-2 text-sm text-rose-700',
    };

    statusEl.className = classMap[kind];
    statusEl.textContent = message;
}

async function ensureOwner(): Promise<void> {
    const me = await callApi<AuthMeResponse>('/api/auth/me', 'GET');

    if (!me.authenticated || me.user?.role !== 'owner') {
        throw new Error('Owner account is required to use this page.');
    }
}

function renderPetOptions(pets: Pet[]): void {
    if (!petSelectEl) return;

    if (pets.length === 0) {
        petSelectEl.innerHTML = '<option value="">No pet found. Please add a pet first.</option>';
        petSelectEl.disabled = true;
        setStatus('No pet profile found. Please create a pet first.', 'error');
        return;
    }

    petSelectEl.disabled = false;
    petSelectEl.innerHTML = [
        '<option value="">Select pet from your list</option>',
        ...pets.map((pet) => {
            const speciesText = pet.species?.name ? ` (${pet.species.name})` : '';
            return `<option value="${pet.id}">${pet.name}${speciesText}</option>`;
        }),
    ].join('');

    setStatus(`Loaded ${pets.length} pet(s).`, 'success');
}

async function loadOwnerPets(): Promise<void> {
    const response = await callApi<{ data: Pet[] }>('/api/owner/pets', 'GET');
    renderPetOptions(response.data);
}

function formatAppointmentDate(value: string): string {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
}

function renderAppointments(appointments: Appointment[]): void {
    if (!appointmentsListEl) return;

    if (appointments.length === 0) {
        appointmentsListEl.innerHTML = '<p>No appointments yet. Create your first appointment above.</p>';
        return;
    }

    appointmentsListEl.innerHTML = appointments
        .map((appointment) => {
            const petName = appointment.pet?.name ?? 'Unknown pet';
            const species = appointment.pet?.species?.name ? ` (${appointment.pet.species.name})` : '';
            const reason = appointment.reason ? `<p class="text-xs text-[#4A4A4A]">Reason: ${appointment.reason}</p>` : '';
            const canCancel = appointment.status === 'pending' || appointment.status === 'confirmed';

            return `
                <div class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-[#333333]">${petName}${species}</p>
                            <p class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">${formatAppointmentDate(appointment.appointment_at)} | ${appointment.status}</p>
                            ${reason}
                        </div>
                        ${canCancel ? `<button type="button" data-action="cancel" data-id="${appointment.id}" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#B42318] hover:text-[#B42318]">Cancel</button>` : ''}
                    </div>
                </div>
            `;
        })
        .join('');

    appointmentsListEl.querySelectorAll<HTMLButtonElement>('button[data-action="cancel"]').forEach((button) => {
        button.addEventListener('click', async () => {
            const appointmentId = Number(button.dataset.id);

            if (!window.confirm('Cancel this appointment?')) {
                return;
            }

            try {
                await callApi(`/api/owner/appointments/${appointmentId}`, 'DELETE');
                await loadAppointments();
                setStatus('Appointment cancelled successfully.', 'success');
            } catch (error) {
                setStatus((error as Error).message, 'error');
            }
        });
    });
}

async function loadAppointments(): Promise<void> {
    const response = await callApi<{ data: Appointment[] }>('/api/owner/appointments', 'GET');
    renderAppointments(response.data);
}

function getCreatePayload(): Record<string, unknown> {
    if (!appointmentForm) return {};

    const formData = new FormData(appointmentForm);
    const get = (key: string): string => String(formData.get(key) ?? '').trim();
    const hour = get('appointment_hour') || '09';
    const minute = get('appointment_minute') || '00';

    return {
        pet_id: Number(get('pet_id')),
        appointment_date: get('appointment_date'),
        appointment_time: `${hour}:${minute}`,
        reason: get('reason') || null,
    };
}

async function bootstrap(): Promise<void> {
    if (!petSelectEl || !appointmentForm || !appointmentsListEl) return;

    try {
        await ensureOwner();
        await loadOwnerPets();
        await loadAppointments();
    } catch (error) {
        setStatus((error as Error).message, 'error');
        return;
    }

    appointmentForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            await callApi('/api/owner/appointments', 'POST', getCreatePayload());
            appointmentForm.reset();
            await loadAppointments();
            setStatus('Appointment created successfully.', 'success');
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

onMounted(() => {
    bootstrap();
});
</script>

<template>
    <div class="hidden"></div>
</template>
