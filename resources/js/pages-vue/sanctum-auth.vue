<script setup lang="ts">
import { onMounted } from 'vue';
import { login, registerOwner } from '../auth/service';
import { createStatusUpdater } from '../auth/status';

function initLoginPage(): void {
    const loginForm = document.getElementById('sanctum-login-form') as HTMLFormElement | null;
    if (!loginForm) return;

    const setStatus = createStatusUpdater();

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(loginForm);
        const payload = {
            email: String(formData.get('email') ?? '').trim(),
            password: String(formData.get('password') ?? ''),
        };

        try {
            const data = await login(payload);
            setStatus(data.message ?? 'Login successful.', 'success');
            loginForm.reset();

            const role = data.user?.role;
            if (role === 'owner') {
                window.location.href = '/owner/overview';
            } else if (role === 'receptionist') {
                window.location.href = '/receptionist/dashboard';
            } else if (role === 'admin') {
                window.location.href = '/admin/dashboard';
            } else if (role === 'vet') {
                window.location.href = '/vet/dashboard';
            } else {
                window.location.href = '/';
            }
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

function initRegisterPage(): void {
    const registerForm = document.getElementById('sanctum-register-form') as HTMLFormElement | null;
    if (!registerForm) return;

    const setStatus = createStatusUpdater();

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(registerForm);
        const payload = {
            name: String(formData.get('name') ?? '').trim(),
            email: String(formData.get('email') ?? '').trim(),
            password: String(formData.get('password') ?? ''),
        };

        try {
            const data = await registerOwner(payload);
            setStatus(data.message ?? 'Register successful.', 'success');
            registerForm.reset();

            if (data.user?.role === 'owner') {
                window.location.href = '/owner/pets';
            }
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

onMounted(() => {
    initLoginPage();
    initRegisterPage();
});
</script>

<template>
    <div class="hidden"></div>
</template>
