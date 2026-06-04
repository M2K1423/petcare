<template>
  <template v-if="!isLoading">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Thanh toán & hóa đơn</h1>
        <p class="text-sm text-gray-500">Xác nhận đơn thuốc, tạo thanh toán, thu tiền và xử lý các hóa đơn y tế chưa thanh toán.</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Đơn thuốc</h2>
            <div class="space-y-4">
                <div v-if="medicineOrders.length === 0" class="rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500">
                    Không có đơn thuốc nào.
                </div>
                
                <article v-for="order in medicineOrders" :key="order.id" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Order #{{ order.id }} - {{ order.pet?.name ?? 'Unknown pet' }}</h3>
                            <p class="mt-1 text-xs text-gray-500">Owner: {{ order.owner?.name ?? 'Unknown' }} ({{ order.owner?.phone ?? 'N/A' }})</p>
                            <p class="mt-1 text-xs text-gray-500">Status: {{ order.status.toUpperCase() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-[#2A6496]">{{ formatCurrency(Number(order.total_amount || 0)) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Payment: {{ order.payment?.status?.toUpperCase() ?? 'NOT CREATED' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                        <div v-if="!order.items || order.items.length === 0">No items.</div>
                        <p v-for="(item, idx) in order.items" :key="idx">
                            {{ item.medicine?.name ?? 'Medicine' }} x {{ item.quantity }} - {{ formatCurrency(Number(item.line_total || 0)) }}
                        </p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button v-if="order.status === 'pending'" @click="confirmMedicineOrder(order.id)" class="rounded-lg bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780]">Confirm Order & Create Payment</button>
                        <button v-if="order.status === 'confirmed' && order.payment?.status !== 'paid'" @click="collectMedicinePayment(order.id, 'cash')" class="rounded-lg bg-[#0F8A5F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C734F]">Collect Cash</button>
                        <button v-if="order.status === 'confirmed' && order.payment?.status !== 'paid'" @click="collectMedicinePayment(order.id, 'vnpay')" class="rounded-lg bg-[#0055A6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#00427F]">Pay with VNPay</button>
                        <template v-if="order.payment?.status === 'paid'">
                            <span class="inline-flex items-center rounded-lg bg-[#ECFDF3] px-4 py-2 text-sm font-semibold text-[#027A48]">
                                {{ order.payment?.gateway_transaction_no ? `Paid - VNPAY ${order.payment.gateway_transaction_no}` : order.payment?.transaction_code ? `Paid - ${order.payment.transaction_code}` : 'Paid' }}
                            </span>
                            <button @click="printInvoice(order.payment.id)" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 flex items-center gap-2">
                                <Printer class="w-4 h-4" /> In Hóa Đơn
                            </button>
                        </template>
                    </div>
                </article>
            </div>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Hóa đơn chưa thanh toán</h2>
            <div class="space-y-3">
                <div v-if="unpaidBills.length === 0" class="rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500">
                    Không có hóa đơn chưa thanh toán.
                </div>
                
                <div v-for="bill in unpaidBills" :key="bill.id" class="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div>
                        <h3 class="font-bold text-gray-900">Record #{{ bill.id }} - {{ bill.appointment?.pet?.name || 'Unknown Pet' }}</h3>
                        <p class="mt-1 text-xs text-gray-500">Owner: {{ bill.appointment?.owner?.name }} ({{ bill.appointment?.owner?.phone || 'N/A' }})</p>
                        <p class="mt-1 text-xs text-gray-500">Diagnosis: {{ bill.diagnosis || 'N/A' }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="text-lg font-bold text-red-600">{{ formatCurrency(Number(bill.total_cost || 0)) }}</span>
                        <button @click="openPaymentModal(bill.id, bill.total_cost)" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Process Payment</button>
                    </div>
                </div>
            </div>
        </article>
    </div>
    
    <div v-if="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Xử lý thanh toán</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Ghi nhận thanh toán cho dịch vụ y tế.</p>
                    </div>
                </div>
                <form @submit.prevent="processPayment" class="mt-5 sm:mt-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Số tiền đã thanh toán (VND)</label>
                        <input v-model="paymentForm.amount" type="number" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Phương thức thanh toán</label>
                        <select v-model="paymentForm.method" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                            <option value="cash">Tiền mặt</option>
                            <option value="credit_card">Thẻ tín dụng</option>
                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                            <option value="vnpay">VNPay</option>
                            <option value="momo">MoMo</option>
                        </select>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm">
                            Xác nhận thanh toán
                        </button>
                        <button @click="showPaymentModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Printer } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const unpaidBills = ref([]);
const medicineOrders = ref([]);
const showPaymentModal = ref(false);

const paymentForm = reactive({
    id: null,
    amount: 0,
    method: 'cash'
});

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function showPaymentNotice() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('paymentStatus');
    const message = params.get('paymentMessage');

    if (!status || !message) return;

    if (status === 'success') {
        notifySuccess(message);
    } else {
        notifyError(new Error(message), message);
    }
    
    params.delete('paymentStatus');
    params.delete('paymentMessage');
    const nextUrl = `${window.location.pathname}${params.toString() ? `?${params.toString()}` : ''}`;
    window.history.replaceState({}, '', nextUrl);
}

async function loadData() {
    try {
        const [unpaidRes, ordersRes] = await Promise.all([
            callApi('/api/receptionist/payments/unpaid', 'GET'),
            callApi('/api/receptionist/medicine-orders', 'GET')
        ]);
        
        unpaidBills.value = unpaidRes?.data || unpaidRes?.data?.data || [];
        medicineOrders.value = ordersRes?.data || [];
    } catch (e) {
        handleApiError(e);
    }
}

async function confirmMedicineOrder(orderId) {
    try {
        await callApi(`/api/receptionist/medicine-orders/${orderId}/confirm`, 'PATCH', {
            payment_method: 'cash',
        });
        notifySuccess('Order confirmed and payment created.');
        await loadData();
    } catch (error) {
        handleApiError(error);
    }
}

async function collectMedicinePayment(orderId, method = 'cash') {
    try {
        const response = await callApi(`/api/receptionist/medicine-orders/${orderId}/collect-payment`, 'PATCH', {
            payment_method: method,
        });

        if (response?.payment_url) {
            window.location.href = response.payment_url;
            return;
        }

        notifySuccess('Payment collected successfully.');
        await loadData();
        
        if (response?.data?.payment?.id) {
            printInvoice(response.data.payment.id);
        }
    } catch (error) {
        handleApiError(error);
    }
}

function printInvoice(paymentId) {
    window.open(`/api/receptionist/payments/${paymentId}/invoice/pdf`, '_blank');
}

function openPaymentModal(id, amount) {
    paymentForm.id = id;
    paymentForm.amount = Number(amount || 0).toFixed(2);
    paymentForm.method = 'cash';
    showPaymentModal.value = true;
}

async function processPayment() {
    if (!paymentForm.id || !paymentForm.amount) return;

    try {
        const response = await callApi('/api/receptionist/payments', 'POST', {
            appointment_id: Number(paymentForm.id),
            amount: Number(paymentForm.amount),
            payment_method: paymentForm.method,
        });

        if (response?.payment_url) {
            window.location.href = response.payment_url;
            return;
        }

        notifySuccess('Payment processed successfully!');
        showPaymentModal.value = false;
        await loadData();

        if (response?.data?.id) {
            printInvoice(response.data.id);
        }
    } catch (e) {
        handleApiError(e);
    }
}

onMounted(() => {
    showPaymentNotice();
    loadData().finally(() => {
        isLoading.value = false;
    });
});
</script>
