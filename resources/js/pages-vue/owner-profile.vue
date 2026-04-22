<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type AuthUser = {
    id: number;
    name: string;
    email: string;
    role: string | null;
};

type AuthMeResponse = {
    authenticated?: boolean;
    user?: AuthUser | null;
};

type UpdateProfileResponse = {
    message?: string;
    user?: AuthUser | null;
};

const statusEl = document.getElementById('owner-profile-status');
const formEl = document.getElementById('owner-profile-form') as HTMLFormElement | null;
const nameEl = document.getElementById('owner-profile-name') as HTMLInputElement | null;
const emailEl = document.getElementById('owner-profile-email') as HTMLInputElement | null;
const roleEl = document.getElementById('owner-profile-role');
const idEl = document.getElementById('owner-profile-id');

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

function renderUser(user: AuthUser): void {
    if (nameEl) nameEl.value = user.name ?? '';
    if (emailEl) emailEl.value = user.email ?? '';
    if (roleEl) roleEl.textContent = user.role ? user.role.toUpperCase() : 'N/A';
    if (idEl) idEl.textContent = String(user.id ?? '-');
}

async function loadProfile(): Promise<AuthUser> {
    const response = await callApi<AuthMeResponse>('/api/auth/me', 'GET');

    if (!response.authenticated || !response.user) {
        throw new Error('Your session has expired. Please login again.');
    }

    if (response.user.role !== 'owner') {
        throw new Error('Owner account is required to use this page.');
    }

    renderUser(response.user);
    return response.user;
}

async function bootstrap(): Promise<void> {
    if (!formEl || !nameEl || !emailEl) return;

    try {
        await loadProfile();
        setStatus('Your profile is ready to edit.');
    } catch (error) {
        setStatus((error as Error).message, 'error');
        return;
    }

    formEl.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            name: nameEl.value.trim(),
            email: emailEl.value.trim(),
        };

        try {
            const response = await callApi<UpdateProfileResponse>('/api/auth/profile', 'PUT', payload);

            if (response.user) {
                renderUser(response.user);
            }

            setStatus(response.message ?? 'Profile updated successfully.', 'success');
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
