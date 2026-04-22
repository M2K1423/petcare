<script setup lang="ts">
import { onMounted } from 'vue';
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

const statusEl = document.getElementById('owner-pets-status');
const petForm = document.getElementById('owner-pet-form') as HTMLFormElement | null;
const petListEl = document.getElementById('owner-pets-list');
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

function toDateOnly(value: string): string {
    if (!value) return value;

    const [datePart] = value.split('T');
    return datePart || value;
}

function formatPetMeta(pet: Pet): string {
    const chunks: string[] = [];

    if (pet.species?.name) chunks.push(`Species: ${pet.species.name}`);
    chunks.push(`Gender: ${pet.gender}`);
    if (pet.weight) chunks.push(`Weight: ${pet.weight} kg`);
    if (pet.birth_date) chunks.push(`Birth date: ${toDateOnly(pet.birth_date)}`);

    return chunks.join(' | ');
}

async function ensureOwner(): Promise<void> {
    const me = await callApi<AuthMeResponse>('/api/auth/me', 'GET');

    if (!me.authenticated || me.user?.role !== 'owner') {
        throw new Error('Owner account is required to use this page.');
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

function renderPets(pets: Pet[]): void {
    if (!petListEl) return;

    if (pets.length === 0) {
        petListEl.innerHTML = '<p class="rounded-xl border border-dashed border-[#DDE1E6] bg-[#F9F9FB] p-4 text-sm text-[#4A4A4A]">No pets found. Create your first pet from the form.</p>';
        return;
    }

    petListEl.innerHTML = pets
        .map((pet) => {
            return `
                <div class="rounded-xl border border-[#DDE1E6] bg-[#FFFFFF] p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-[#333333]">${pet.name}</p>
                            <p class="mt-1 text-sm text-[#4A4A4A]">${formatPetMeta(pet)}</p>
                            ${pet.notes ? `<p class="mt-2 text-sm text-[#4A4A4A]">Notes: ${pet.notes}</p>` : ''}
                        </div>
                        <div class="flex gap-2">
                            <a href="/owner/pets/${pet.id}/edit" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Edit</a>
                            <a href="/owner/pets/${pet.id}/health-records" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Health records</a>
                            <button data-action="delete" data-id="${pet.id}" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Delete</button>
                        </div>
                    </div>
                </div>
            `;
        })
        .join('');

    petListEl.querySelectorAll<HTMLButtonElement>('button[data-action="delete"]').forEach((button) => {
        button.addEventListener('click', async () => {
            const petId = Number(button.dataset.id);
            const shouldDelete = window.confirm('Delete this pet?');
            if (!shouldDelete) return;

            try {
                await callApi(`/api/owner/pets/${petId}`, 'DELETE');
                setStatus('Pet deleted successfully.', 'success');
                await loadPets();
            } catch (error) {
                setStatus((error as Error).message, 'error');
            }
        });
    });
}

async function loadPets(): Promise<void> {
    const response = await callApi<{ data: Pet[] }>('/api/owner/pets', 'GET');
    renderPets(response.data);
}

async function bootstrap(): Promise<void> {
    if (!petForm || !petListEl || !speciesSelect) return;

    try {
        await ensureOwner();
        await loadSpecies();
        await loadPets();
        setStatus('Ready.', 'neutral');
    } catch (error) {
        setStatus((error as Error).message, 'error');
        return;
    }

    petForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        try {
            const payload = normalizeFormPayload(petForm);
            await callApi('/api/owner/pets', 'POST', payload);
            petForm.reset();

            if (speciesSelect.options.length > 0) {
                speciesSelect.selectedIndex = 0;
            }

            setStatus('Pet created successfully.', 'success');
            await loadPets();
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
