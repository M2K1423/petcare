<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm thanh toán..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <select v-model="statusFilter" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả trạng thái</option>
        <option value="pending">Chờ Xác Nhận</option>
        <option value="completed">Hoàn Tất</option>
        <option value="failed">Thất Bại</option>
        <option value="refunded">Hoàn Tiền</option>
      </select>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
        <div class="text-sm text-gray-600">Chờ Xác Nhận</div>
        <div class="text-2xl font-bold text-yellow-600">{{ pendingCount }}</div>
        <div class="text-xs text-gray-500">{{ formatCurrency(pendingTotal) }}</div>
      </div>
      <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-400">
        <div class="text-sm text-gray-600">Hoàn Tất</div>
        <div class="text-2xl font-bold text-green-600">{{ completedCount }}</div>
        <div class="text-xs text-gray-500">{{ formatCurrency(completedTotal) }}</div>
      </div>
      <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
        <div class="text-sm text-gray-600">Thất Bại</div>
        <div class="text-2xl font-bold text-red-600">{{ failedCount }}</div>
      </div>
      <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
        <div class="text-sm text-gray-600">Hoàn Tiền</div>
        <div class="text-2xl font-bold text-blue-600">{{ refundedCount }}</div>
        <div class="text-xs text-gray-500">{{ formatCurrency(refundedTotal) }}</div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Mã Thanh Toán</th>
            <th class="px-6 py-3 text-left">Người Dùng</th>
            <th class="px-6 py-3 text-left">Phương Thức</th>
            <th class="px-6 py-3 text-right">Số Tiền</th>
            <th class="px-6 py-3 text-left">Ngày</th>
            <th class="px-6 py-3 text-left">Trạng Thái</th>
            <th class="px-6 py-3 text-left">Hành Động</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="payment in filteredPayments" :key="payment.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ payment.transaction_id || payment.id }}</td>
            <td class="px-6 py-4">{{ payment.user?.name || 'N/A' }}</td>
            <td class="px-6 py-4">{{ payment.payment_method }}</td>
            <td class="px-6 py-4 text-right font-semibold text-blue-600">{{ formatCurrency(payment.amount) }}</td>
            <td class="px-6 py-4">{{ formatDate(payment.created_at) }}</td>
            <td class="px-6 py-4">
              <span :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                payment.status === 'completed' ? 'bg-green-100 text-green-800' :
                payment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                payment.status === 'failed' ? 'bg-red-100 text-red-800' :
                'bg-blue-100 text-blue-800'
              ]">
                {{ statusLabel(payment.status) }}
              </span>
            </td>
            <td class="px-6 py-4 space-x-2 text-sm">
              <button v-if="payment.status === 'pending'" @click="confirmPayment(payment)" class="text-green-600 hover:text-green-800">✓</button>
              <button v-if="payment.status === 'completed'" @click="refundPayment(payment)" class="text-orange-600 hover:text-orange-800">↩️</button>
              <button @click="viewDetails(payment)" class="text-blue-600 hover:text-blue-800">👁️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Details Modal -->
    <div v-if="showDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Chi Tiết Thanh Toán</h3>
        
        <div class="space-y-3 mb-6">
          <div class="flex justify-between">
            <span class="text-gray-600">Mã:</span>
            <span class="font-semibold">{{ selectedPayment?.transaction_id }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Số Tiền:</span>
            <span class="font-semibold">{{ formatCurrency(selectedPayment?.amount) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Phương Thức:</span>
            <span class="font-semibold">{{ selectedPayment?.payment_method }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Trạng Thái:</span>
            <span class="font-semibold">{{ statusLabel(selectedPayment?.status) }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Ngày:</span>
            <span class="font-semibold">{{ formatDate(selectedPayment?.created_at) }}</span>
          </div>
        </div>

        <button @click="showDetail = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400">
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const payments = ref([]);
const search = ref('');
const statusFilter = ref('');
const showDetail = ref(false);
const selectedPayment = ref(null);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const statusLabel = (status) => {
  const labels = {
    'pending': 'Chờ Xác Nhận',
    'completed': 'Hoàn Tất',
    'failed': 'Thất Bại',
    'refunded': 'Hoàn Tiền'
  };
  return labels[status] || status;
};

const filteredPayments = computed(() => {
  let result = payments.value;
  if (search.value) {
    const q = search.value.toLowerCase();
    result = result.filter(p =>
      p.transaction_id?.toLowerCase().includes(q) ||
      p.user?.name?.toLowerCase().includes(q)
    );
  }
  if (statusFilter.value) {
    result = result.filter(p => p.status === statusFilter.value);
  }
  return result;
});

const pendingCount = computed(() => payments.value.filter(p => p.status === 'pending').length);
const pendingTotal = computed(() => payments.value.filter(p => p.status === 'pending').reduce((sum, p) => sum + p.amount, 0));
const completedCount = computed(() => payments.value.filter(p => p.status === 'completed').length);
const completedTotal = computed(() => payments.value.filter(p => p.status === 'completed').reduce((sum, p) => sum + p.amount, 0));
const failedCount = computed(() => payments.value.filter(p => p.status === 'failed').length);
const refundedCount = computed(() => payments.value.filter(p => p.status === 'refunded').length);
const refundedTotal = computed(() => payments.value.filter(p => p.status === 'refunded').reduce((sum, p) => sum + p.amount, 0));

const fetchPayments = async () => {
  try {
    const res = await fetch('/api/admin/payments', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    payments.value = data.data;
  } catch (err) {
    console.error('Lỗi tải thanh toán:', err);
  }
};

const confirmPayment = async (payment) => {
  try {
    await fetch(`/api/admin/payments/${payment.id}/confirm`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    fetchPayments();
  } catch (err) {
    console.error('Lỗi xác nhận thanh toán:', err);
  }
};

const refundPayment = (payment) => {
  const reason = prompt('Lý do hoàn tiền:');
  if (reason) {
    fetch(`/api/admin/payments/${payment.id}/refund`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ reason })
    }).then(() => fetchPayments());
  }
};

const viewDetails = (payment) => {
  selectedPayment.value = payment;
  showDetail.value = true;
};

onMounted(fetchPayments);
</script>
