export type StatusType = 'neutral' | 'success' | 'error';

export function createStatusUpdater(elementId = 'sanctum-status') {
    const statusEl = document.getElementById(elementId);

    return function setStatus(message: string, type: StatusType = 'neutral'): void {
        if (!statusEl) return;

        const classes = {
            neutral: 'mt-4 text-sm text-slate-600',
            success: 'mt-4 text-sm text-emerald-700',
            error: 'mt-4 text-sm text-rose-700',
        };

        statusEl.className = classes[type];
        statusEl.textContent = message;
    };
}
