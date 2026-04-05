import { callApi } from './http';
import { clearToken, saveToken } from './token';
import type { AuthPayload } from './types';

type RegisterPayload = {
    name: string;
    email: string;
    password: string;
};

type LoginPayload = {
    email: string;
    password: string;
};

export async function registerOwner(payload: RegisterPayload): Promise<AuthPayload> {
    const data = await callApi<AuthPayload>('/api/auth/register', 'POST', payload);

    if (data.token) {
        saveToken(data.token);
    }

    return data;
}

export async function login(payload: LoginPayload): Promise<AuthPayload> {
    const data = await callApi<AuthPayload>('/api/auth/login', 'POST', payload);

    if (data.token) {
        saveToken(data.token);
    }

    return data;
}

export async function logout(): Promise<void> {
    await callApi<{ message?: string }>('/api/auth/logout', 'POST');
    clearToken();
}

export async function me(): Promise<AuthPayload> {
    return callApi<AuthPayload>('/api/auth/me', 'GET');
}
