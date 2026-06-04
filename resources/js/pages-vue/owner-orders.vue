<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-6">
      <!-- Section header with gradient line -->
      <div class="border-b border-slate-100 pb-4">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">📋 Đơn thuốc của tôi</h1>
        <p class="text-sm text-slate-400 mt-1">Theo dõi chi tiết các đơn thuốc được kê, trạng thái xác nhận và lịch sử thanh toán.</p>
      </div>

      <!-- MỚI: Thanh tìm kiếm nhanh đơn hàng -->
      <div class="mb-4 relative">
        <input v-model="searchQuery" type="text" placeholder="Tìm theo mã đơn (#12), tên bé cưng, trạng thái đơn..." class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 py-2.5 text-xs text-slate-700 outline-none transition duration-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
        <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4" />
      </div>

      <div class="space-y-6">
        <div v-if="filteredOrders.length === 0" class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/50">
          <span class="text-5xl mb-3">📄</span>
          <p class="text-sm font-bold text-slate-500">Không tìm thấy đơn hàng nào phù hợp.</p>
        </div>
        
        <article v-for="order in filteredOrders" :key="order.id" class="rounded-3xl border border-slate-100 bg-[#FCFDFE] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:border-slate-200 transition-all duration-300">
          <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-4 mb-4">
            <div>
              <p class="text-base font-extrabold text-slate-700">Đơn hàng #{{ order.id }} - Bé {{ order.pet?.name ?? 'thú cưng không rõ' }}</p>
              <p class="mt-1 text-xs text-slate-400 flex items-center gap-1">
                <Clock class="h-3.5 w-3.5 text-slate-400" />
                Ngày tạo đơn: {{ formatDateTime(order.created_at) }}
              </p>
            </div>
            <div class="flex flex-col items-end gap-1.5">
              <span :class="['inline-flex rounded-full px-3 py-1 text-xs font-bold border', getOrderTone(order.status)]">
                {{ getOrderStatusLabel(order.status) }}
              </span>
              <p class="text-lg font-extrabold text-indigo-600">{{ formatCurrency(order.total_amount) }}</p>
            </div>
          </div>

          <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <!-- Order Items Card -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
              <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400 border-b border-slate-50 pb-2 flex items-center gap-1.5">
                <span>📦</span> Chi tiết thuốc đã đặt
              </p>
              <div class="mt-4 space-y-4">
                <div v-for="item in (order.items || [])" :key="item.id" class="flex items-center justify-between gap-3 border-b border-slate-50/50 pb-3 last:border-b-0 last:pb-0">
                  <div>
                    <p class="text-sm font-bold text-slate-700">{{ item.medicine?.name ?? 'Thuốc y tế' }}</p>
                    <p class="text-xs text-slate-400 mt-1">Số lượng: {{ item.quantity }} {{ item.medicine?.unit || 'hộp' }} x {{ formatCurrency(item.unit_price) }}</p>
                  </div>
                  <p class="text-sm font-bold text-indigo-600">{{ formatCurrency(item.line_total) }}</p>
                </div>
              </div>
            </div>

            <!-- Payment details Card -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
              <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400 border-b border-slate-50 pb-2 flex items-center gap-1.5">
                <span>💳</span> Thông tin thanh toán
              </p>
              <div class="mt-4 space-y-2 text-xs text-slate-500 leading-relaxed">
                <p class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Trạng thái thanh toán:</span>
                  <span class="font-bold text-indigo-600">{{ getPaymentStatusLabel(order.payment?.status || order.status) }}</span>
                </p>
                <p class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Phương thức:</span>
                  <span class="font-bold text-slate-700">{{ getPaymentMethodLabel(order.payment?.payment_method) }}</span>
                </p>
                <p class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Tổng tiền đơn thuốc:</span>
                  <span class="font-bold text-slate-700">{{ order.payment?.amount ? formatCurrency(order.payment.amount) : formatCurrency(order.total_amount) }}</span>
                </p>
                <p v-if="order.confirmed_at" class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Ngày lễ tân xác nhận:</span>
                  <span class="font-bold text-slate-700">{{ formatDateTime(order.confirmed_at) }}</span>
                </p>
                <p v-if="order.confirmer?.name" class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Lễ tân xác nhận:</span>
                  <span class="font-bold text-slate-700">{{ order.confirmer.name }}</span>
                </p>
                <p v-if="order.payment?.paid_at || order.paid_at" class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Thời gian thanh toán:</span>
                  <span class="font-bold text-slate-700">{{ formatDateTime(order.payment?.paid_at ?? order.paid_at) }}</span>
                </p>
                <p v-if="order.payment?.transaction_code" class="flex justify-between border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Mã giao dịch (VNPay):</span>
                  <span class="font-bold text-slate-700">{{ order.payment.transaction_code }}</span>
                </p>
                <p v-if="order.notes" class="flex flex-col gap-1 border-b border-slate-50 pb-2">
                  <span class="font-semibold text-slate-600">Ghi chú đơn thuốc:</span>
                  <span class="text-slate-700 bg-slate-50 p-2 rounded-xl border border-slate-100/50 mt-1">{{ order.notes }}</span>
                </p>
                
                <div v-if="order.payment?.status === 'pending'" class="mt-4 pt-2">
                  <button @click="collectOwnerPayment(order.id, 'vnpay')" class="inline-flex w-full items-center justify-center rounded-xl bg-[#0055A6] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#00427F] disabled:opacity-50 transition-all duration-200" :disabled="isPaying">
                    {{ isPaying ? 'Đang kết nối cổng thanh toán...' : '💳 Thanh toán trực tuyến VNPay' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, Search } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifyError } = useNotification();

const isLoading = ref(true);
const isPaying = ref(false);
const orders = ref([]);
const searchQuery = ref('');

const filteredOrders = computed(() => {
    if (!searchQuery.value) return orders.value;
    const q = searchQuery.value.toLowerCase().trim();
    return orders.value.filter(order => {
        const orderId = `#${order.id}`;
        const petName = (order.pet?.name || '').toLowerCase();
        const status = (order.status || '').toLowerCase();
        const statusText = getOrderStatusLabel(order.status).toLowerCase();
        return orderId.includes(q) || petName.includes(q) || status.includes(q) || statusText.includes(q);
    });
});

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} đ`;
}

function formatDateTime(input) {
    if (!input) return 'Chưa rõ';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString('vi-VN', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getOrderStatusLabel(status) {
  switch (status) {
    case 'paid': return 'Đã thanh toán';
    case 'confirmed': return 'Đã xác nhận';
    case 'cancelled': return 'Đã hủy';
    case 'pending': return 'Chờ duyệt';
    default: return status;
  }
}

function getPaymentStatusLabel(status) {
  switch (status) {
    case 'paid': return 'Đã thanh toán thành công ✅';
    case 'confirmed': return 'Đã duyệt đơn hàng';
    case 'cancelled': return 'Đã hủy giao dịch';
    case 'pending': return 'Chờ thanh toán ⏳';
    default: return 'Đang chờ xử lý';
  }
}

function getPaymentMethodLabel(method) {
  if (!method) return 'Chưa thanh toán';
  switch (method) {
    case 'vnpay': return 'Cổng thanh toán VNPay';
    case 'cash': return 'Thanh toán tại phòng khám';
    default: return method;
  }
}

function getOrderTone(status) {
    if (status === 'paid') return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    if (status === 'confirmed') return 'bg-sky-50 text-sky-600 border-sky-100';
    if (status === 'cancelled') return 'bg-rose-50 text-rose-600 border-rose-100';
    return 'bg-amber-50 text-amber-600 border-amber-100';
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
            notifyError('Không thể tạo đường dẫn thanh toán.');
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
        notifyError('Tải danh sách đơn hàng thất bại.');
    } finally {
        isLoading.value = false;
    }
}

const onGlobalSearch = (e) => {
    searchQuery.value = e.detail;
};

onMounted(() => {
    loadData();
    window.addEventListener('petcare-global-search', onGlobalSearch);
    const globalSearchInput = document.getElementById('global-search-input');
    if (globalSearchInput) {
        searchQuery.value = globalSearchInput.value;
    }
});

onUnmounted(() => {
    window.removeEventListener('petcare-global-search', onGlobalSearch);
});
</script>

<style scoped>
/* tactile active clicks */
button:active {
  transform: scale(0.98);
}
</style>
