<template>
  <template v-if="!isLoading">
    <section class="mt-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">Đơn thuốc của tôi</h1>
                <p class="mt-2 text-sm text-[#4A4A4A]">Theo dõi trạng thái xác nhận và thanh toán.</p>
            </div>
        </div>

        <div class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
            <p v-if="orders.length === 0">No medicine orders yet.</p>
            
            <article v-for="order in orders" :key="order.id" class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.03)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-bold text-[#333333]">Order #{{ order.id }} for {{ order.pet?.name ?? 'Unknown pet' }}</p>
                        <p class="mt-1 text-xs text-[#4A4A4A]">Created: {{ formatDateTime(order.created_at) }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span :class="['inline-flex rounded-full px-3 py-1 text-xs font-semibold', getOrderTone(order.status)]">{{ order.status.toUpperCase() }}</span>
                        <p class="text-lg font-extrabold text-[#2A6496]">{{ formatCurrency(order.total_amount) }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Order Items</p>
                        <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                            <div v-for="item in (order.items || [])" :key="item.id" class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#333333]">{{ item.medicine?.name ?? 'Medicine' }}</p>
                                    <p class="text-xs text-[#64748B]">{{ item.quantity }} x {{ formatCurrency(item.unit_price) }} {{ item.medicine?.unit ? `/ ${item.medicine.unit}` : '' }}</p>
                                </div>
                                <p class="font-semibold text-[#2A6496]">{{ formatCurrency(item.line_total) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Payment History</p>
                        <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                            <p><span class="font-semibold text-[#333333]">Payment status:</span> {{ order.payment?.status?.toUpperCase() ?? 'WAITING FOR RECEPTIONIST' }}</p>
                            <p><span class="font-semibold text-[#333333]">Payment method:</span> {{ order.payment?.payment_method ?? 'N/A' }}</p>
                            <p><span class="font-semibold text-[#333333]">Amount:</span> {{ order.payment?.amount ? formatCurrency(order.payment.amount) : formatCurrency(order.total_amount) }}</p>
                            <p><span class="font-semibold text-[#333333]">Confirmed at:</span> {{ formatDateTime(order.confirmed_at) }}</p>
                            <p><span class="font-semibold text-[#333333]">Confirmed by:</span> {{ order.confirmer?.name ?? 'N/A' }}</p>
                            <p><span class="font-semibold text-[#333333]">Paid at:</span> {{ formatDateTime(order.payment?.paid_at ?? order.paid_at) }}</p>
                            <p><span class="font-semibold text-[#333333]">Transaction code:</span> {{ order.payment?.transaction_code ?? 'N/A' }}</p>
                            <p><span class="font-semibold text-[#333333]">Order notes:</span> {{ order.notes ?? 'N/A' }}</p>
                            <p><span class="font-semibold text-[#333333]">Payment notes:</span> {{ order.payment?.notes ?? 'N/A' }}</p>
                            
                            <div v-if="order.payment?.status === 'pending'" class="mt-3">
                                <button @click="collectOwnerPayment(order.id, 'vnpay')" class="rounded-lg bg-[#0055A6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#00427F] disabled:opacity-50 transition" :disabled="isPaying">
                                    {{ isPaying ? 'Processing...' : 'Pay with VNPay' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifyError } = useNotification();

const isLoading = ref(true);
const isPaying = ref(false);
const orders = ref([]);

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function formatDateTime(input) {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString();
}

function getOrderTone(status) {
    if (status === 'paid') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

async function collectOwnerPayment(orderId, paymentMethod) {
    isPaying.value = true;
    try {
        const payload = {
            order_id: orderId,
            payment_method: paymentMethod
        };
        const response = await callApi('/api/owner/payments/create-url', 'POST', payload);
        if (response.payment_url) {
            window.location.href = response.payment_url;
        } else {
            notifyError('Failed to get payment URL.');
            isPaying.value = false;
        }
    } catch (error) {
        notifyError(error.message);
        isPaying.value = false;
    }
}

async function loadData() {
    try {
        const resp = await callApi('/api/owner/medicine-orders', 'GET');
        orders.value = resp.data || [];
    } catch (error) {
        notifyError('Failed to load orders.');
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    loadData();
});
</script>
