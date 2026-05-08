import { getToken } from './token';

let csrfReady = false;

function getCookieValue(name: string): string {
    const cookies = document.cookie ? document.cookie.split('; ') : [];

    for (const cookie of cookies) {
        const [key, ...parts] = cookie.split('=');
        if (key === name) {
            return decodeURIComponent(parts.join('='));
        }
    }

    return '';
}

async function ensureCsrfCookie(): Promise<void> {
    if (csrfReady) return;

    await fetch('/sanctum/csrf-cookie', {
        method: 'GET',
        headers: {
            Accept: 'application/json',
        },
        credentials: 'same-origin',
    });

    csrfReady = true;
}

export async function callApi<T = any>(url: string, method: string, body?: unknown): Promise<T> {
    const token = getToken();
    const upperMethod = method.toUpperCase();

    if (upperMethod !== 'GET') {
        await ensureCsrfCookie();
    }

    const xsrfToken = getCookieValue('XSRF-TOKEN');

    const response = await fetch(url, {
        method: upperMethod,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: body ? JSON.stringify(body) : undefined,
        credentials: 'same-origin',
    });

    const payload = await response.json().catch(() => ({} as Record<string, unknown>));

    if (!response.ok) {
        const message = (payload as { message?: string }).message ?? `Request failed (${response.status})`;
        throw new Error(message);
    }

    return payload as T;
}
