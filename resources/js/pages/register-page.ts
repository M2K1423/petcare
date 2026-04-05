import { registerOwner } from '../auth/service';
import { createStatusUpdater } from '../auth/status';

export function initRegisterPage(): void {
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
