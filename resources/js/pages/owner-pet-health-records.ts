import { callApi } from '../auth/http';

type AuthMeResponse = {
    authenticated?: boolean;
    user?: { role?: string | null } | null;
};

type PetSummary = {
    id: number;
    name: string;
    breed: string | null;
    species?: { id: number; name: string } | null;
};

type MedicalRecord = {
    id: number;
    record_date: string;
    symptoms: string | null;
    diagnosis: string | null;
    treatment: string | null;
    notes: string | null;
};

type Vaccination = {
    id: number;
    vaccine_name: string;
    vaccinated_on: string;
    next_due_on: string | null;
    batch_number: string | null;
    notes: string | null;
};

type HealthRecordsResponse = {
    data: {
        pet: PetSummary;
        medical_records: MedicalRecord[];
        vaccinations: Vaccination[];
    };
};

declare global {
    interface Window {
        __OWNER_PET_HEALTH_RECORDS__?: {
            petId?: number;
        };
    }
}

const statusEl = document.getElementById('pet-health-records-status');
const petEl = document.getElementById('pet-health-records-pet');
const medicalListEl = document.getElementById('pet-medical-records-list');
const vaccinationListEl = document.getElementById('pet-vaccinations-list');

function setStatus(message: string, kind: 'neutral' | 'success' | 'error' = 'neutral'): void {
    if (!statusEl) return;

    const classMap = {
        neutral: 'mt-4 text-sm text-[#4A4A4A]',
        success: 'mt-4 text-sm text-emerald-700',
        error: 'mt-4 text-sm text-rose-700',
    };

    statusEl.className = classMap[kind];
    statusEl.textContent = message;
}

function formatDate(input: string): string {
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleDateString();
}

function renderPet(pet: PetSummary): void {
    if (!petEl) return;

    const speciesText = pet.species?.name ? ` (${pet.species.name})` : '';
    const breedText = pet.breed ? ` - ${pet.breed}` : '';
    petEl.textContent = `Pet: ${pet.name}${speciesText}${breedText}`;
}

function renderMedicalRecords(records: MedicalRecord[]): void {
    if (!medicalListEl) return;

    if (records.length === 0) {
        medicalListEl.innerHTML = '<p class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-3">Chua co benh an cho thu cung nay.</p>';
        return;
    }

    medicalListEl.innerHTML = records
        .map((record) => {
            return `
                <article class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4">
                    <p class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">Ngay ghi nhan: ${formatDate(record.record_date)}</p>
                    <p class="mt-2"><span class="font-semibold text-[#333333]">Trieu chung:</span> ${record.symptoms || '-'}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Chan doan:</span> ${record.diagnosis || '-'}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Dieu tri:</span> ${record.treatment || '-'}</p>
                    <p class="mt-1"><span class="font-semibold text-[#333333]">Ghi chu:</span> ${record.notes || '-'}</p>
                </article>
            `;
        })
        .join('');
}

function renderVaccinations(vaccinations: Vaccination[]): void {
    if (!vaccinationListEl) return;

    if (vaccinations.length === 0) {
        vaccinationListEl.innerHTML = '<p class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-3">Chua co lich tiem phong.</p>';
        return;
    }

    vaccinationListEl.innerHTML = vaccinations
        .map((item) => {
            const nextDue = item.next_due_on ? formatDate(item.next_due_on) : '-';
            return `
                <article class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4">
                    <p class="font-semibold text-[#333333]">${item.vaccine_name}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Tiem ngay: ${formatDate(item.vaccinated_on)}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Mui tiep theo: ${nextDue}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">So lo: ${item.batch_number || '-'}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Ghi chu: ${item.notes || '-'}</p>
                </article>
            `;
        })
        .join('');
}

async function ensureOwner(): Promise<void> {
    const me = await callApi<AuthMeResponse>('/api/auth/me', 'GET');

    if (!me.authenticated || me.user?.role !== 'owner') {
        throw new Error('Owner account is required to use this page.');
    }
}

async function bootstrap(): Promise<void> {
    const petId = Number(window.__OWNER_PET_HEALTH_RECORDS__?.petId ?? 0);

    if (!petId) {
        setStatus('Invalid pet id.', 'error');
        return;
    }

    try {
        await ensureOwner();

        const response = await callApi<HealthRecordsResponse>(`/api/owner/pets/${petId}/health-records`, 'GET');
        renderPet(response.data.pet);
        renderMedicalRecords(response.data.medical_records);
        renderVaccinations(response.data.vaccinations);
        setStatus('Loaded medical records and vaccination schedule.', 'success');
    } catch (error) {
        setStatus((error as Error).message, 'error');
    }
}

bootstrap();
