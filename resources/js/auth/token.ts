const TOKEN_STORAGE_KEY = 'petcare_sanctum_token';

export function saveToken(token: string): void {
    localStorage.setItem(TOKEN_STORAGE_KEY, token);
}

export function getToken(): string {
    return localStorage.getItem(TOKEN_STORAGE_KEY) ?? '';
}

export function clearToken(): void {
    localStorage.removeItem(TOKEN_STORAGE_KEY);
}
