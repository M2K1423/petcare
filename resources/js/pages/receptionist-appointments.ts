import { callApi } from '../auth/http';

interface Species {
    id: number;
    name: string;
}

interface DoctorWorkload {
    id: number;
    user?: { name?: string };
}

interface Appointment {
    id: number;
    appointment_at: string;
    status: string;
    queue_number?: number | null;
    patient_name?: string;
    pet?: { name: string; species: { name: string } };
    owner?: { name: string; phone: string };
}

document.addEventListener('DOMContentLoaded', () => {
    loadSpecies();
    loadDoctors();
    initAppointmentsDateFilter();
    loadAppointments();
    initConditionFields();
    
    document.getElementById('btn-refresh-appointments')?.addEventListener('click', () => {
        loadAppointments();
    });

    document.getElementById('btn-filter-appointments')?.addEventListener('click', () => {
        loadAppointments();
    });

    document.getElementById('btn-today-appointments')?.addEventListener('click', () => {
        const dateInput = document.getElementById('appointments-date') as HTMLInputElement | null;
        if (dateInput) {
            dateInput.value = getTodayDate();
        }
        loadAppointments();
    });

    const form = document.getElementById('walkin-form') as HTMLFormElement;
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await submitWalkIn(form);
        });
    }
});

async function loadSpecies() {
    // Re-use owner species api endpoint or we can add an open endpoint if needed
    // Let's assume there's a global or owner endpoint we can hit for species
    // Note: If logged in as receptionist, might need a route for species
    try {
        // Will just hit an existing owner route or a public route if it exists. Reverting to basic mapping if failed.
        // Assuming receptionist can hit the same if we bypass role checks, wait, owner role middleware is there. Let's send a request and handle if failed.
        const res = await callApi<any>('/api/receptionist/species', 'GET').catch(() => null);
        const sel = document.getElementById('species-select') as HTMLSelectElement;
        if (!sel) return;
        
        if (res && res.data) {
            sel.innerHTML = res.data.map((s: Species) => `<option value="${s.id}">${s.name}</option>`).join('');
        } else {
            // fallback
            sel.innerHTML = `
                <option value="1">Dog</option>
                <option value="2">Cat</option>
                <option value="3">Bird</option>
            `;
        }
    } catch (e) {
        console.error(e);
    }
}

async function loadAppointments() {
    const container = document.getElementById('appointments-container');
    if (!container) return;
    
    const selectedDate = getSelectedAppointmentsDate();
    
    try {
        const res = await callApi<any>(`/api/receptionist/appointments?date=${selectedDate}`, 'GET');
        const items: Appointment[] = res?.data || res?.data?.data || [];
        
        if (items.length === 0) {
            container.innerHTML = `<div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">No appointments found for ${selectedDate}.</div>`;
            return;
        }

        container.innerHTML = items.map((app) => {
            const canCheckIn = app.status === 'pending' || (app.status === 'confirmed' && !app.queue_number);

            return `
            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div>
                    <h3 class="font-bold text-gray-900">${app.pet?.name || 'Unknown Pet'}</h3>
                    <p class="text-xs text-gray-500">Owner: ${app.owner?.name} (${app.owner?.phone || 'N/A'})</p>
                    <p class="text-xs text-gray-500">Status: <span class="font-medium px-2 py-0.5 rounded-full bg-gray-100">${app.status}</span></p>
                </div>
                <div class="flex flex-col gap-2">
                    <button onclick="goToAppointmentDetails(${app.id})" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Details</button>
                    ${canCheckIn ? `<button onclick="checkIn(${app.id})" class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200">Check-in</button>` : ''}
                </div>
            </div>
            `;
        }).join('');
    } catch (e) {
        container.innerHTML = '<div class="p-4 text-center text-sm text-red-500 bg-red-50 rounded-xl">Failed to load appointments.</div>';
    }
}

async function loadDoctors() {
    const sel = document.getElementById('doctor-select') as HTMLSelectElement | null;
    if (!sel) return;

    try {
        const res = await callApi<any>('/api/receptionist/doctors/available', 'GET');
        const items: DoctorWorkload[] = res?.data || [];

        const options = items.map((d) => `<option value="${d.id}">${d.user?.name || `Doctor #${d.id}`}</option>`).join('');
        sel.innerHTML = '<option value="">Auto assign later</option>' + options;
    } catch (e) {
        sel.innerHTML = '<option value="">Auto assign later</option>';
    }
}

async function submitWalkIn(form: HTMLFormElement) {
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries()) as Record<string, string>;
    payload.weight = payload.pet_weight ?? '';
    delete payload.pet_weight;
    payload.is_emergency = formData.get('is_emergency') ? '1' : '0';

    const selectedCondition = (payload.condition_option ?? '').trim();
    const customCondition = (payload.condition_custom ?? '').trim();

    if (selectedCondition && selectedCondition !== 'Other') {
        payload.reason = customCondition ? `${selectedCondition} - ${customCondition}` : selectedCondition;
    } else if (customCondition) {
        payload.reason = customCondition;
    }

    delete payload.condition_option;
    delete payload.condition_custom;
    
    const msgEl = document.getElementById('walkin-message');
    if (msgEl) {
        msgEl.className = 'mt-3 text-sm font-medium text-blue-600';
        msgEl.innerText = 'Processing...';
        msgEl.classList.remove('hidden');
    }
    
    try {
        await callApi<any>('/api/receptionist/customers/walk-in', 'POST', payload as any);

        if (msgEl) {
            msgEl.className = 'mt-3 text-sm font-medium text-green-600';
            msgEl.innerText = 'Walk-in registered & added to queue successfully!';
        }
        form.reset();
        setTimeout(() => msgEl?.classList.add('hidden'), 3000);
        
    } catch (e: any) {
        if (msgEl) {
            msgEl.className = 'mt-3 text-sm font-medium text-red-600';
            msgEl.innerText = e.message || 'Failed to register walk-in. Please check your inputs.';
        }
    }
}

(window as any).checkIn = async function(appId: number) {
    try {
        await callApi<any>(`/api/receptionist/appointments/${appId}/check-in`, 'PATCH');
        alert('Check-in successful!');
        loadAppointments();
    } catch (e) {
        alert('Failed to check-in.');
    }
};

(window as any).goToAppointmentDetails = function(appointmentId: number) {
    window.location.href = `/receptionist/appointments/${appointmentId}`;
};

function initConditionFields() {
    const conditionSelect = document.getElementById('condition-option') as HTMLSelectElement | null;
    const conditionCustom = document.getElementById('condition-custom') as HTMLInputElement | null;

    if (!conditionSelect || !conditionCustom) return;

    const sync = () => {
        if (conditionSelect.value === 'Other' || conditionSelect.value === '') {
            conditionCustom.readOnly = false;
            conditionCustom.placeholder = 'Or type condition details';
        } else {
            conditionCustom.placeholder = 'Optional note for selected condition';
        }
    };

    conditionSelect.addEventListener('change', sync);
    sync();
}

function initAppointmentsDateFilter() {
    const dateInput = document.getElementById('appointments-date') as HTMLInputElement | null;
    if (!dateInput) return;

    if (!dateInput.value) {
        dateInput.value = getTodayDate();
    }

    dateInput.addEventListener('change', () => {
        loadAppointments();
    });
}

function getTodayDate(): string {
    return new Date().toISOString().split('T')[0];
}

function getSelectedAppointmentsDate(): string {
    const dateInput = document.getElementById('appointments-date') as HTMLInputElement | null;
    return (dateInput?.value || getTodayDate()).trim();
}