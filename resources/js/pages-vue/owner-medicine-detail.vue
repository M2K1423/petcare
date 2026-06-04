<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-6">
      <!-- Header with back navigation -->
      <header class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Không gian chủ nuôi</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-800">Chi tiết sản phẩm thuốc</h1>
          </div>
          <a href="/owner/shop" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 hover:border-slate-300">
            <ArrowLeft class="h-4 w-4" />
            Quay lại cửa hàng
          </a>
        </div>
      </header>

      <!-- Main Product Detail Section -->
      <section class="rounded-3xl border border-slate-100 bg-white p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur">
        <div v-if="!medicine" class="py-16 text-center">
          <span class="text-4xl">⚠️</span>
          <p class="text-sm font-bold text-slate-500 mt-2">Không tìm thấy thông tin sản phẩm.</p>
        </div>

        <div v-else class="grid gap-8 md:grid-cols-12">
          <!-- Left Column: Product Image & Badges -->
          <div class="md:col-span-5 flex flex-col gap-4">
            <div class="aspect-[4/3] bg-slate-50 border border-slate-100 rounded-3xl overflow-hidden flex items-center justify-center relative w-full shadow-inner group">
              <!-- Warning Badge overlay -->
              <span v-if="getMedicineWarning(medicine)" :class="getWarningClass(getMedicineWarning(medicine))" class="absolute top-4 right-4 text-[10px] font-extrabold px-3 py-1 rounded-full border shadow-md z-10 uppercase tracking-wider">
                {{ getMedicineWarning(medicine) }}
              </span>

              <img :src="medicine.image_url || getFallbackImage(medicine)" :alt="medicine.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>

            <!-- Meta attributes list -->
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100/50 space-y-3">
              <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Mã sản phẩm (SKU):</span>
                <span class="font-mono text-slate-700 bg-white border border-slate-200 px-2 py-0.5 rounded-md font-bold">{{ medicine.sku || 'N/A' }}</span>
              </div>
              <div class="flex justify-between items-center text-xs" v-if="medicine.expiration_date">
                <span class="text-slate-400 font-semibold">Hạn sử dụng:</span>
                <span class="text-slate-700 font-bold">{{ formatDate(medicine.expiration_date) }}</span>
              </div>
              <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Trạng thái kho:</span>
                <span :class="medicine.stock_quantity > 0 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100'" class="px-2.5 py-0.5 rounded-full font-bold border text-[10px]">
                  {{ medicine.stock_quantity > 0 ? `Còn hàng (${medicine.stock_quantity} ${medicine.unit || 'hộp'})` : 'Hết hàng' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Right Column: Product details and purchasing controls -->
          <div class="md:col-span-7 flex flex-col justify-between">
            <div class="space-y-4">
              <div>
                <span v-if="medicine.category" class="text-xs font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 border border-indigo-100/50 px-3 py-1 rounded-full">
                  {{ medicine.category }}
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-3 leading-tight">{{ medicine.name }}</h2>
              </div>

              <!-- Price section -->
              <div class="border-y border-slate-100 py-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-indigo-600 leading-none">{{ formatCurrency(medicine.price) }}</span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">/ {{ medicine.unit || 'hộp' }}</span>
              </div>

              <!-- Description -->
              <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Mô tả sản phẩm</h3>
                <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line bg-slate-50/50 border border-slate-100 p-4 rounded-2xl">
                  {{ medicine.description || 'Không có mô tả chi tiết cho sản phẩm thuốc thú y này. Vui lòng tham khảo ý kiến của bác sĩ thú y trước khi sử dụng.' }}
                </p>
              </div>
            </div>

            <!-- Action inputs -->
            <div class="mt-8 border-t border-slate-100 pt-6 space-y-4">
              <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col">
                  <span class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Chọn số lượng</span>
                  <div class="flex items-center gap-2 border border-slate-200 rounded-xl p-1 bg-slate-50 w-36">
                    <button type="button" @click="decQty" class="h-9 w-9 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-indigo-600 active:scale-95 transition" :disabled="quantity <= 1 || medicine.stock_quantity <= 0">
                      <Minus class="h-4 w-4" />
                    </button>
                    <input v-model.number="quantity" type="number" min="1" :max="medicine.stock_quantity" class="w-10 bg-transparent text-center font-bold text-sm text-slate-700 border-none outline-none select-none" readonly :disabled="medicine.stock_quantity <= 0">
                    <button type="button" @click="incQty" class="h-9 w-9 rounded-lg bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-indigo-600 active:scale-95 transition" :disabled="quantity >= medicine.stock_quantity || medicine.stock_quantity <= 0">
                      <Plus class="h-4 w-4" />
                    </button>
                  </div>
                </div>

                <div class="text-right">
                  <span class="text-xs text-slate-400 font-semibold block mb-1">Tổng cộng dự tính:</span>
                  <span class="text-xl font-extrabold text-slate-700">{{ formatCurrency(medicine.price * quantity) }}</span>
                </div>
              </div>

              <!-- Button actions -->
              <div class="flex gap-4">
                <button @click="addToCart" type="button" class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-extrabold text-white shadow-lg shadow-indigo-600/10 hover:bg-indigo-700 hover:shadow-indigo-600/20 active:scale-[0.98] transition-all duration-200" :disabled="medicine.stock_quantity <= 0">
                  <ShoppingCart class="h-5 w-5 shrink-0" />
                  Thêm vào giỏ hàng
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ArrowLeft, Minus, Plus, ShoppingCart } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const medicine = ref(null);
const quantity = ref(1);

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} đ`;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' });
    } catch {
        return dateStr;
    }
}

function getFallbackImage(med) {
    const label = encodeURIComponent(med.name);
    return `https://placehold.co/480x320/EEF2F6/4F46E5?text=${label}`;
}

function getMedicineWarning(med) {
    if (med.stock_quantity <= 0) return 'Hết hàng';
    if (med.stock_quantity <= 5) return 'Sắp hết';
    if (!med.expiration_date) return '';

    const today = new Date();
    const current = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const expiry = new Date(med.expiration_date);
    const diffDays = Math.ceil((expiry.getTime() - current.getTime()) / 86400000);

    if (diffDays < 0) return 'Hết hạn';
    if (diffDays <= 30) return 'Sắp hết hạn';
    return '';
}

function getWarningClass(warning) {
    switch (warning) {
        case 'Hết hàng':
        case 'Hết hạn':
            return 'bg-rose-50 text-rose-600 border-rose-100';
        case 'Sắp hết':
        case 'Sắp hết hạn':
            return 'bg-amber-50 text-amber-600 border-amber-100';
        default:
            return 'bg-slate-50 text-slate-500 border-slate-100';
    }
}

function incQty() {
    if (medicine.value && quantity.value < medicine.value.stock_quantity) {
        quantity.value++;
    }
}

function decQty() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

function addToCart() {
    if (!medicine.value) return;
    
    const qty = Math.max(1, Math.min(quantity.value, medicine.value.stock_quantity || 1));
    const cart = window.cartStore;
    if (!cart) {
        notifyError('Giỏ hàng không khả dụng.');
        return;
    }

    cart.addItem({
        medicine: {
            id: medicine.value.id,
            name: medicine.value.name,
            price: medicine.value.price,
            unit: medicine.value.unit,
        },
        quantity: qty,
    });

    notifySuccess(`Đã thêm ${qty} ${medicine.value.unit || 'hộp'} ${medicine.value.name} vào giỏ hàng.`);
}

async function loadData() {
    const mountNode = document.querySelector('[data-page="owner-medicine-detail"]');
    const medicineId = Number(mountNode?.dataset?.medicineId);
    
    if (!medicineId) {
        notifyError('Mã sản phẩm không hợp lệ.');
        isLoading.value = false;
        return;
    }

    try {
        const response = await callApi(`/api/owner/medicines/${medicineId}`, 'GET');
        medicine.value = response.data;
        if (medicine.value && medicine.value.stock_quantity <= 0) {
            quantity.value = 0;
        }
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    loadData();
});
</script>

<style scoped>
/* Disable input spinners */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>
