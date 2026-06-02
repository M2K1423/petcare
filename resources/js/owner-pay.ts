import { callApi } from './auth/http';
import { useNotification } from './composables/useNotification';

const { notifyError } = useNotification();

// Attach a helper to window so pages can trigger owner payments (VNPay etc.)
(window as any).collectOwnerPayment = async function (orderId: number, method: string = 'vnpay') {
    try {
        const response = await callApi(`/api/owner/medicine-orders/${orderId}/pay`, 'PATCH', {
            payment_method: method,
        });

        if (response?.payment_url) {
            window.location.href = response.payment_url;
            return;
        }

        // fallback: reload to reflect updated status
        window.location.reload();
    } catch (err: any) {
        const message = err?.message || 'Failed to initiate payment.';
        notifyError(message);
    }
};

export {};
