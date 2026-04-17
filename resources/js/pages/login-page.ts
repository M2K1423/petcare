import { login } from '../auth/service';
import { createStatusUpdater } from '../auth/status';

export function initLoginPage(): void {
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
