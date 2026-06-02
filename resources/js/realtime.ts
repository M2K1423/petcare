import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { me } from './auth/service';
import { getToken } from './auth/token';

declare global {
    interface Window {
        Echo?: Echo;
        Pusher?: typeof Pusher;
        __petcareRealtimeBooted?: boolean;
    }
}

type AuthMeResponse = {
    authenticated?: boolean;
    user?: {
        id?: number;
        role?: string | null;
    } | null;
};

async function bootRealtime(): Promise<void> {
    const token = getToken();

    if (!token || window.__petcareRealtimeBooted) {
        return;
    }

    let meResponse: AuthMeResponse | null = null;

    try {
        meResponse = (await me()) as AuthMeResponse;
    } catch {
        return;
    }

    const userId = meResponse?.user?.id;

    if (!meResponse?.authenticated || !userId) {
        return;
    }

    window.Pusher = Pusher;

    const connection = import.meta.env.VITE_BROADCAST_CONNECTION || 'reverb';

    if (connection === 'pusher' || import.meta.env.VITE_PUSHER_APP_KEY) {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
            forceTLS: true,
            authEndpoint: '/api/broadcasting/auth',
            auth: {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            },
        });
    } else {
        const host = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
        const port = Number(import.meta.env.VITE_REVERB_PORT || 8080);
        const scheme = import.meta.env.VITE_REVERB_SCHEME || (window.location.protocol === 'https:' ? 'https' : 'http');

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: host,
            wsPort: port,
            wssPort: port,
            forceTLS: scheme === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/api/broadcasting/auth',
            auth: {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            },
        });
    }

    window.Echo.private(`users.${userId}.notifications`).listen('.notification.created', (event: { notification?: unknown }) => {
        window.dispatchEvent(new CustomEvent('petcare-notification-received', {
            detail: event.notification ?? event,
        }));
    });

    // Subscribe chat user channel để nhận thông báo phiên chat mới/đóng
    window.Echo.private(`chat.user.${userId}`).listen('.chat.session.updated', (event: unknown) => {
        window.dispatchEvent(new CustomEvent('petcare-chat-session-updated', { detail: event }));
    });

    window.__petcareRealtimeBooted = true;
}

export function subscribeChatSession(sessionId: number, onMessage: (msg: unknown) => void): void {
    if (!window.Echo) return;
    window.Echo.private(`chat.session.${sessionId}`).listen('.chat.message.sent', onMessage);
}

export function unsubscribeChatSession(sessionId: number): void {
    if (!window.Echo) return;
    window.Echo.leave(`chat.session.${sessionId}`);
}

void bootRealtime();
