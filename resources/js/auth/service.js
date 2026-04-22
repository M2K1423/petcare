import { callApi } from './http';
import { clearToken, saveToken } from './token';

export async function registerOwner(payload) {
    const data = await callApi('/api/auth/register', 'POST', payload);

    if (data.token) {
        saveToken(data.token);
    }

    return data;
}

export async function login(payload) {
    const data = await callApi('/api/auth/login', 'POST', payload);

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
