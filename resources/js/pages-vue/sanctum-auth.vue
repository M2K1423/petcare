<script setup lang="ts">
import { onMounted } from 'vue';
import { login, registerOwner, forgotPassword, resetPassword } from '../auth/service';
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
                window.location.href = '/admin/medicines';
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

function initForgotPasswordPage(): void {
    const forgotForm = document.getElementById('sanctum-forgot-form') as HTMLFormElement | null;
    if (!forgotForm) return;

    const setStatus = createStatusUpdater();

    forgotForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(forgotForm);
        const email = String(formData.get('email') ?? '').trim();

        try {
            const data = await forgotPassword(email);
            let successMsg = data.message ?? 'Mã OTP đã được gửi.';
            if (data.otp_demo) {
                successMsg += ` [OTP Test: ${data.otp_demo}]`;
            }
            setStatus(successMsg, 'success');
            forgotForm.reset();

            setTimeout(() => {
                window.location.href = '/sanctum-auth/reset-password?email=' + encodeURIComponent(email);
            }, 3000);
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

function initResetPasswordPage(): void {
    const resetForm = document.getElementById('sanctum-reset-form') as HTMLFormElement | null;
    if (!resetForm) return;

    const setStatus = createStatusUpdater();

    // Điền email từ query string nếu có
    const urlParams = new URLSearchParams(window.location.search);
    const emailInput = document.getElementById('email') as HTMLInputElement | null;
    if (emailInput && urlParams.has('email')) {
        emailInput.value = urlParams.get('email') ?? '';
    }

    resetForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(resetForm);
        const payload = {
            email: String(formData.get('email') ?? '').trim(),
            otp: String(formData.get('otp') ?? '').trim(),
            password: String(formData.get('password') ?? ''),
            password_confirmation: String(formData.get('password_confirmation') ?? ''),
        };

        try {
            const data = await resetPassword(payload);
            setStatus(data.message ?? 'Khôi phục mật khẩu thành công.', 'success');
            resetForm.reset();

            setTimeout(() => {
                window.location.href = '/sanctum-auth';
            }, 2000);
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}

onMounted(() => {
    initLoginPage();
    initRegisterPage();
    initForgotPasswordPage();
    initResetPasswordPage();
});
</script>

<template>
    <div class="hidden"></div>
</template>
