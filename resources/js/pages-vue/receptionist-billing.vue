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
                    <p class="text-xs text-gray-500">Owner: ${bill.appointment?.owner?.name} (${bill.appointment?.owner?.phone || 'N/A'})</p>
                    <p class="text-xs text-gray-500 mt-1">Diagnosis: ${bill.diagnosis || 'N/A'}</p>
                </div>
                <div class="flex flex-col gap-2 items-end">
                    <span class="text-lg font-bold text-red-600">$${Number(bill.total_cost || 0).toFixed(2)}</span>
                    <button onclick="openPaymentModal(${bill.id}, ${bill.total_cost})" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Process Payment</button>
                </div>
            </div>
        `).join('');
    } catch (e) {
        container.innerHTML = '<div class="p-4 text-center text-sm text-red-500 bg-red-50 rounded-xl">Failed to load unpaid bills.</div>';
    }
}

(window as any).openPaymentModal = function(id: number, amount: number) {
    const modal = document.getElementById('payment-modal');
    const idInput = document.getElementById('appointment_id') as HTMLInputElement;
    const amountInput = document.getElementById('amount') as HTMLInputElement;

    if (modal && idInput && amountInput) {
        idInput.value = id.toString();
        amountInput.value = Number(amount || 0).toFixed(2);
        modal.classList.remove('hidden');
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
        await callApi<any>('/api/receptionist/payments', 'POST', {
            appointment_id: Number(payload.appointment_id),
            amount: Number(payload.amount),
            payment_method: payload.payment_method || 'cash',
        } as any);

        alert('Payment processed successfully!');
        hideModal();
        loadUnpaidBills();
    } catch (e: any) {
        alert(e.message || 'Failed to process payment.');
    }
}

onMounted(() => {
    loadUnpaidBills();

    const form = document.getElementById('payment-form') as HTMLFormElement;
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
