<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Order = any;

const historyEl = document.getElementById('owner-order-history');

function formatCurrency(value: number | string): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function formatDateTime(input?: string | null): string {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString();
}

function getOrderTone(status: string): string {
    if (status === 'paid') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

function renderHistory(orders: Order[]): void {
    if (!historyEl) return;

    if (orders.length === 0) {
        historyEl.innerHTML = '<p>No medicine orders yet.</p>';
        return;
    }

    historyEl.innerHTML = orders.map((order) => `
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.03)]">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-base font-bold text-[#333333]">Order #${order.id} for ${order.pet?.name ?? 'Unknown pet'}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Created: ${formatDateTime(order.created_at)}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${getOrderTone(order.status)}">${order.status.toUpperCase()}</span>
                    <p class="text-lg font-extrabold text-[#2A6496]">${formatCurrency(order.total_amount)}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Order Items</p>
                    <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                        ${(order.items ?? []).map((item) => `
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#333333]">${item.medicine?.name ?? 'Medicine'}</p>
                                    <p class="text-xs text-[#64748B]">${item.quantity} x ${formatCurrency(item.unit_price)} ${item.medicine?.unit ? `/ ${item.medicine.unit}` : ''}</p>
                                </div>
                                <p class="font-semibold text-[#2A6496]">${formatCurrency(item.line_total)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Payment History</p>
                    <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                        <p><span class="font-semibold text-[#333333]">Payment status:</span> ${order.payment?.status?.toUpperCase() ?? 'WAITING FOR RECEPTIONIST'}</p>
                        <p><span class="font-semibold text-[#333333]">Payment method:</span> ${order.payment?.payment_method ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Amount:</span> ${order.payment?.amount ? formatCurrency(order.payment.amount) : formatCurrency(order.total_amount)}</p>
                        <p><span class="font-semibold text-[#333333]">Confirmed at:</span> ${formatDateTime(order.confirmed_at)}</p>
                        <p><span class="font-semibold text-[#333333]">Confirmed by:</span> ${order.confirmer?.name ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Paid at:</span> ${formatDateTime(order.payment?.paid_at ?? order.paid_at)}</p>
                        <p><span class="font-semibold text-[#333333]">Transaction code:</span> ${order.payment?.transaction_code ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Order notes:</span> ${order.notes ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Payment notes:</span> ${order.payment?.notes ?? 'N/A'}</p>
                        ${order.payment?.status === 'pending' ? `<div class="mt-3"><button onclick="collectOwnerPayment(${order.id}, 'vnpay')" class="rounded-lg bg-[#0055A6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#00427F]">Pay with VNPay</button></div>` : ''}
                    </div>
                </div>
            </div>
        </article>
    `).join('');
}

async function loadPage(): Promise<void> {
    try {
        const resp = await callApi<{ data: Order[] }>('/api/owner/medicine-orders', 'GET');
        renderHistory(resp.data);
    } catch (error) {
        if (historyEl) historyEl.innerHTML = '<div class="rounded-2xl bg-red-50 p-4 text-center text-sm text-red-500">Failed to load orders.</div>';
    }
}

onMounted(() => {
    loadPage().catch(() => {
        if (historyEl) historyEl.innerHTML = '<div class="rounded-2xl bg-red-50 p-4 text-center text-sm text-red-500">Failed to load orders.</div>';
    });
});
</script>

<template>
    <div class="hidden"></div>
</template>
