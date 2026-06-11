import { callApi } from './http';
import { clearToken, saveToken } from './token';

export async function registerOwner(payload: Record<string, unknown>) {
    const data = await callApi<{ message?: string; token?: string; user?: { role?: string } }>('/api/auth/register', 'POST', payload);

    if (data.token) {
        saveToken(data.token);
    }

    return data;
}

export async function login(payload: Record<string, unknown>) {
    const data = await callApi<{ message?: string; token?: string; user?: { role?: string } }>('/api/auth/login', 'POST', payload);

    if (data.token) {
        saveToken(data.token);
    }

    return data;
}

export async function logout() {
    await callApi('/api/auth/logout', 'POST');
    clearToken();
}

export async function me() {
    return callApi('/api/auth/me', 'GET');
}

export async function forgotPassword(email: string) {
    return callApi<{ message?: string; otp_demo?: string }>('/api/auth/forgot-password', 'POST', { email });
}

export async function resetPassword(payload: Record<string, unknown>) {
    return callApi<{ message?: string }>('/api/auth/reset-password', 'POST', payload);
}
