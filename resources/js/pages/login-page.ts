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
        } catch (error) {
            setStatus((error as Error).message, 'error');
        }
    });
}
