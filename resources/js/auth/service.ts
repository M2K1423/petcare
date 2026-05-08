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
