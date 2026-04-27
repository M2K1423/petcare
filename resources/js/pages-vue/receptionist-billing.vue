<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

interface UnpaidBill {
    id: number;
    diagnosis: string;
    total_cost: number;
    appointment?: {
        pet?: { name: string; species: { name: string } };
        owner?: { name: string; phone: string };
        doctor?: { user: { name: string } };
    };
}

interface MedicineOrder {
    id: number;
    status: string;
    total_amount: number;
    notes?: string | null;
    owner?: { name: string; phone?: string | null };
    pet?: { name: string };
    items?: Array<{
        quantity: number;
        line_total: number;
        medicine?: { name: string; unit?: string | null };
    }>;
    payment?: {
        status: string;
        amount: number;
        payment_method?: string | null;
        transaction_code?: string | null;
        gateway_transaction_no?: string | null;
        paid_at?: string | null;
    } | null;
}

function formatCurrency(value: number): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function showPaymentNotice(): void {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('paymentStatus');
    const message = params.get('paymentMessage');

    if (!status || !message) return;

    alert(message);
    params.delete('paymentStatus');
    params.delete('paymentMessage');
    const nextUrl = `${window.location.pathname}${params.toString() ? `?${params.toString()}` : ''}`;
    window.history.replaceState({}, '', nextUrl);
}

async function loadUnpaidBills() {
    const container = document.getElementById('unpaid-container');
    if (!container) return;

    try {
        const res = await callApi<any>('/api/receptionist/payments/unpaid', 'GET');
        const items: UnpaidBill[] = res?.data || res?.data?.data || [];

        if (items.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">No unpaid bills found.</div>';
            return;
        }

        container.innerHTML = items.map((bill) => `
            <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div>
                    <h3 class="font-bold text-gray-900">Record #${bill.id} - ${bill.appointment?.pet?.name || 'Unknown Pet'}</h3>
                    <p class="mt-1 text-xs text-gray-500">Owner: ${bill.appointment?.owner?.name} (${bill.appointment?.owner?.phone || 'N/A'})</p>
                    <p class="mt-1 text-xs text-gray-500">Diagnosis: ${bill.diagnosis || 'N/A'}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="text-lg font-bold text-red-600">${formatCurrency(Number(bill.total_cost || 0))}</span>
                    <button onclick="openPaymentModal(${bill.id}, ${bill.total_cost})" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Process Payment</button>
                </div>
            </div>
        `).join('');
    } catch (e) {
        container.innerHTML = '<div class="rounded-xl bg-red-50 p-4 text-center text-sm text-red-500">Failed to load unpaid bills.</div>';
    }
}

async function loadMedicineOrders() {
    const container = document.getElementById('medicine-orders-container');
    if (!container) return;

    try {
        const response = await callApi<{ data: MedicineOrder[] }>('/api/receptionist/medicine-orders', 'GET');
        const orders = response.data;

        if (orders.length === 0) {
            container.innerHTML = '<div class="rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500">No medicine orders found.</div>';
            return;
        }

        container.innerHTML = orders.map((order) => {
            const items = (order.items ?? [])
                .map((item) => `<p>${item.medicine?.name ?? 'Medicine'} x ${item.quantity} - ${formatCurrency(Number(item.line_total || 0))}</p>`)
                .join('');

            const canConfirm = order.status === 'pending';
            const canCollect = order.status === 'confirmed' && order.payment?.status !== 'paid';
            const paidLabel = order.payment?.gateway_transaction_no
                ? `Paid - VNPAY ${order.payment.gateway_transaction_no}`
                : order.payment?.transaction_code
                    ? `Paid - ${order.payment.transaction_code}`
                    : 'Paid';

            return `
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Order #${order.id} - ${order.pet?.name ?? 'Unknown pet'}</h3>
                            <p class="mt-1 text-xs text-gray-500">Owner: ${order.owner?.name ?? 'Unknown'} (${order.owner?.phone ?? 'N/A'})</p>
                            <p class="mt-1 text-xs text-gray-500">Status: ${order.status.toUpperCase()}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-[#2A6496]">${formatCurrency(Number(order.total_amount || 0))}</p>
                            <p class="mt-1 text-xs text-gray-500">Payment: ${order.payment?.status?.toUpperCase() ?? 'NOT CREATED'}</p>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">${items || '<p>No items.</p>'}</div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        ${canConfirm ? `<button onclick="confirmMedicineOrder(${order.id})" class="rounded-lg bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780]">Confirm Order & Create Payment</button>` : ''}
                        ${canCollect ? `<button onclick="collectMedicinePayment(${order.id}, 'cash')" class="rounded-lg bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F]">Collect Cash</button>` : ''}
                        ${canCollect ? `<button onclick="collectMedicinePayment(${order.id}, 'vnpay')" class="rounded-lg bg-[#0055A6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#00427F]">Pay with VNPay</button>` : ''}
                        ${order.payment?.status === 'paid' ? `<span class="inline-flex items-center rounded-lg bg-[#ECFDF3] px-4 py-2 text-sm font-semibold text-[#027A48]">${paidLabel}</span>` : ''}
                    </div>
                </article>
            `;
        }).join('');
    } catch (error) {
        container.innerHTML = '<div class="rounded-xl bg-red-50 p-4 text-center text-sm text-red-500">Failed to load medicine orders.</div>';
    }
}

(window as any).openPaymentModal = function (id: number, amount: number) {
    const modal = document.getElementById('payment-modal');
    const idInput = document.getElementById('appointment_id') as HTMLInputElement | null;
    const amountInput = document.getElementById('amount') as HTMLInputElement | null;

    if (modal && idInput && amountInput) {
        idInput.value = id.toString();
        amountInput.value = Number(amount || 0).toFixed(2);
        modal.classList.remove('hidden');
    }
};

(window as any).confirmMedicineOrder = async function (orderId: number) {
    try {
        await callApi(`/api/receptionist/medicine-orders/${orderId}/confirm`, 'PATCH', {
            payment_method: 'cash',
        });
        alert('Order confirmed and payment created.');
        await loadMedicineOrders();
    } catch (error: any) {
        alert(error.message || 'Failed to confirm order.');
    }
};

(window as any).collectMedicinePayment = async function (orderId: number, method: string = 'cash') {
    try {
        const response = await callApi<any>(`/api/receptionist/medicine-orders/${orderId}/collect-payment`, 'PATCH', {
            payment_method: method,
        });

        if (response?.payment_url) {
            window.location.href = response.payment_url;
            return;
        }

        alert('Payment collected successfully.');
        await loadMedicineOrders();
    } catch (error: any) {
        alert(error.message || 'Failed to collect payment.');
    }
};

function hideModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

async function processPayment(form: HTMLFormElement) {
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    if (!payload.appointment_id || !payload.amount) return;

    try {
        const response = await callApi<any>('/api/receptionist/payments', 'POST', {
            appointment_id: Number(payload.appointment_id),
            amount: Number(payload.amount),
            payment_method: payload.payment_method || 'cash',
        } as any);

        if (response?.payment_url) {
            window.location.href = response.payment_url;
            return;
        }

        alert('Payment processed successfully!');
        hideModal();
        await loadUnpaidBills();
    } catch (e: any) {
        alert(e.message || 'Failed to process payment.');
    }
}

onMounted(() => {
    showPaymentNotice();
    void loadUnpaidBills();
    void loadMedicineOrders();

    const form = document.getElementById('payment-form') as HTMLFormElement | null;
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await processPayment(form);
        });
    }

    const closeBtn = document.getElementById('btn-close-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', hideModal);
    }
});
</script>

<template>
    <div class="hidden"></div>
</template>
