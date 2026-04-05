import { callApi } from '../auth/http';

type Species = {
    id: number;
    name: string;
};

type Pet = {
    id: number;
    name: string;
    species_id: number;
    species?: { id: number; name: string } | null;
    gender: 'male' | 'female' | 'unknown';
    breed: string | null;
    birth_date: string | null;
    weight: string | null;
    color: string | null;
    allergies: string | null;
    notes: string | null;
};

type AuthMeResponse = {
    authenticated?: boolean;
    user?: { role?: string | null } | null;
};

const rootEl = document.getElementById('owner-pet-edit-root') as HTMLDivElement | null;
const statusEl = document.getElementById('owner-pet-edit-status');
const formEl = document.getElementById('owner-pet-edit-form') as HTMLFormElement | null;
const speciesSelect = document.getElementById('pet-species') as HTMLSelectElement | null;

function setStatus(message: string, kind: 'neutral' | 'success' | 'error' = 'neutral'): void {
    if (!statusEl) return;

    const classMap = {
        neutral: 'mt-3 text-sm text-[#4A4A4A]',
        success: 'mt-3 text-sm text-emerald-700',
        error: 'mt-3 text-sm text-rose-700',
    };

    statusEl.className = classMap[kind];
    statusEl.textContent = message;
}

function getPetId(): number {
    const petId = Number(rootEl?.dataset.petId ?? '');

    if (!Number.isInteger(petId) || petId <= 0) {
        throw new Error('Invalid pet id.');
    }

    return petId;
}

function setInputValue(id: string, value: string | null): void {
    const input = document.getElementById(id) as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement | null;
    if (!input) return;

    input.value = value ?? '';
}

function normalizeFormPayload(form: HTMLFormElement): Record<string, unknown> {
    const formData = new FormData(form);
    const get = (key: string): string => String(formData.get(key) ?? '').trim();
    const weightText = get('weight');

    return {
        name: get('name'),
        species_id: Number(get('species_id')),
        gender: get('gender') || 'unknown',
        breed: get('breed') || null,
        birth_date: get('birth_date') || null,
        weight: weightText ? Number(weightText) : null,
        color: get('color') || null,
        allergies: get('allergies') || null,
        notes: get('notes') || null,
    };
}

async function ensureOwner(): Promise<void> {
    const me = await callApi<AuthMeResponse>('/api/auth/me', 'GET');

    if (!me.authenticated || me.user?.role !== 'owner') {
        throw new Error('Owner account is required to edit pets.');
    }
}

async function loadSpecies(): Promise<void> {
    if (!speciesSelect) return;

    const response = await callApi<{ data: Species[] }>('/api/owner/species', 'GET');

    if (response.data.length === 0) {
        speciesSelect.innerHTML = '<option value="">No species data</option>';
        return;
    }

    speciesSelect.innerHTML = response.data
        .map((species) => `<option value="${species.id}">${species.name}</option>`)
        .join('');
}

async function loadPet(petId: number): Promise<Pet> {
    const response = await callApi<{ data: Pet }>(`/api/owner/pets/${petId}`, 'GET');
    const pet = response.data;

    setInputValue('pet-name', pet.name);
    setInputValue('pet-species', String(pet.species_id));
    setInputValue('pet-gender', pet.gender);
    setInputValue('pet-breed', pet.breed);
    setInputValue('pet-birth-date', pet.birth_date);
    setInputValue('pet-weight', pet.weight);
    setInputValue('pet-color', pet.color);
    setInputValue('pet-allergies', pet.allergies);
    setInputValue('pet-notes', pet.notes);

    return pet;
}

async function bootstrap(): Promise<void> {
    if (!rootEl || !formEl || !speciesSelect) return;

    const petId = getPetId();

    try {
        await ensureOwner();
        await loadSpecies();
        const pet = await loadPet(petId);
        setStatus(`Editing: ${pet.name}`, 'neutral');
    } catch (error) {
        setStatus((error as Error).message, 'error');
        return;
    }

    formEl.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            const payload = normalizeFormPayload(formEl);
            await callApi(`/api/owner/pets/${petId}`, 'PUT', payload);
            const updatedPet = await loadPet(petId);
            setStatus(`Saved successfully: ${updatedPet.name}`, 'success');
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

bootstrap();