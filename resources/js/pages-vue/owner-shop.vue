<template>
  <template v-if="!isLoading">
    <div class="flex flex-col gap-6">
      <!-- Section header with gradient line -->
      <div class="border-b border-slate-100 pb-4">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">💊 Cửa hàng thuốc y tế PetCare</h1>
        <p class="text-sm text-slate-400 mt-1">Tìm kiếm và mua trực tuyến các loại thuốc, thực phẩm bổ sung chính hãng được bác sĩ thú y khuyên dùng.</p>
      </div>

      <section class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] backdrop-blur">
        <!-- Interactive Search & Filtering Controls -->
        <div class="grid gap-6 md:grid-cols-[1.2fr_0.8fr] mb-6">
          <div>
            <label for="shop-medicine-search" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Tìm sản phẩm y tế</label>
            <div class="relative mt-2">
              <input v-model="searchQuery" id="shop-medicine-search" type="text" placeholder="Tìm theo tên thuốc, danh mục điều trị, mô tả..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-11 pr-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
              </span>
            </div>
          </div>
          <div>
            <label for="shop-category-filter" class="text-xs font-bold uppercase tracking-[0.12em] text-slate-400">Danh mục điều trị</label>
            <select v-model="selectedCategory" id="shop-category-filter" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-700 outline-none transition duration-300 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
              <option value="all">🧪 Tất cả danh mục</option>
              <option v-for="category in categories" :key="category" :value="category">📦 {{ category }}</option>
            </select>
          </div>
        </div>

        <div class="text-xs text-slate-400 mb-6 flex items-center justify-between border-b border-slate-50 pb-3">
          <span>Đang hiển thị <strong class="text-slate-700">{{ filteredMedicines.length }}</strong> sản phẩm phù hợp.</span>
          <span class="font-semibold text-indigo-500 bg-indigo-50 px-2.5 py-0.5 rounded-full text-[10px]">Kho thuốc PetCare</span>
        </div>

        <!-- Medicine Grid -->
        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <div v-if="filteredMedicines.length === 0" class="col-span-full py-16 flex flex-col items-center justify-center text-center border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/30">
            <span class="text-4xl mb-2">🔍</span>
            <p class="text-sm font-bold text-slate-500">Không tìm thấy sản phẩm thuốc phù hợp.</p>
            <p class="text-xs text-slate-400 mt-1">Vui lòng kiểm tra lại từ khóa tìm kiếm hoặc đổi danh mục khác.</p>
          </div>
          
          <article v-for="medicine in filteredMedicines" :key="medicine.id" class="medicine-card group bg-white border border-slate-100 rounded-2xl p-4 flex flex-col hover:border-indigo-500/50 hover:shadow-[0_12px_30px_rgba(0,0,0,0.04)] transition-all duration-300 h-full relative overflow-hidden">
            <!-- Out of stock overlay -->
            <div v-if="medicine.stock_quantity <= 0" class="absolute inset-0 bg-white/70 flex items-center justify-center font-bold text-rose-500 text-sm z-10 backdrop-blur-[1px]">
              <span class="px-4 py-2 bg-rose-50 rounded-full border border-rose-200 shadow-sm">❌ Hết hàng</span>
            </div>

            <!-- Product image -->
            <div class="aspect-[4/3] bg-slate-50 rounded-xl mb-4 overflow-hidden flex items-center justify-center relative w-full border border-slate-100/50">
              <img :src="medicine.image_url || getFallbackImage(medicine)" :alt="medicine.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              
              <!-- Medicine Warnings badges -->
              <span v-if="getMedicineWarning(medicine)" :class="getWarningClass(getMedicineWarning(medicine))" class="absolute top-2.5 right-2.5 text-[9px] font-bold px-2 py-0.5 rounded-full border shadow-sm z-10">
                {{ getMedicineWarning(medicine) }}
              </span>
            </div>
            
            <div class="flex flex-col flex-1">
              <p v-if="medicine.category" class="text-[9px] font-extrabold uppercase tracking-wider text-indigo-500 mb-1.5">{{ medicine.category }}</p>
              <h3 class="font-extrabold text-slate-700 text-sm line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">{{ medicine.name }}</h3>
              <p class="text-xs text-slate-400 line-clamp-2 mb-4 flex-1 leading-relaxed">{{ medicine.description ?? 'Sản phẩm thuốc thú y chuyên dụng có sẵn tại phòng khám.' }}</p>
              
              <!-- Info block -->
              <div class="flex items-end justify-between mb-4 border-t border-slate-100 pt-3">
                <div>
                  <span class="text-indigo-600 font-extrabold text-lg block leading-tight">{{ formatCurrency(medicine.price) }}</span>
                  <span class="text-[10px] text-slate-400 font-medium">ĐVT: {{ medicine.unit || 'hộp' }}</span>
                </div>
                <div class="text-right">
                  <span class="text-[10px] bg-slate-50 text-slate-500 border border-slate-100 px-2 py-1 rounded-md font-bold whitespace-nowrap">Kho: {{ medicine.stock_quantity }}</span>
                </div>
              </div>
              
              <!-- Purchase controls -->
              <div class="flex items-center gap-2 mt-auto">
                <div class="w-20 shrink-0">
                  <input v-model.number="cartQuantities[medicine.id]" type="number" min="1" :max="medicine.stock_quantity" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-2 py-2 text-center text-sm outline-none transition focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" :disabled="medicine.stock_quantity <= 0">
                </div>
                <button @click="addToCart(medicine)" type="button" class="flex-1 rounded-xl bg-indigo-600 px-3 py-2 text-sm font-bold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:shadow-indigo-600/20 transition-all duration-200 flex items-center justify-center gap-1.5" :disabled="medicine.stock_quantity <= 0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                  <span class="whitespace-nowrap">Thêm</span>
                </button>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const medicines = ref([]);
const cartQuantities = ref({});
const searchQuery = ref('');
const selectedCategory = ref('all');

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} đ`;
}

function getFallbackImage(medicine) {
    const label = encodeURIComponent(medicine.name);
    return `https://placehold.co/320x220/EEF2F6/4F46E5?text=${label}`;
}

function getMedicineWarning(medicine) {
    if (medicine.stock_quantity <= 0) return 'Hết hàng';
    if (medicine.stock_quantity <= 5) return 'Sắp hết';
    if (!medicine.expiration_date) return '';

    const today = new Date();
    const current = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const expiry = new Date(`${medicine.expiration_date}T00:00:00`);
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

const categories = computed(() => {
    return Array.from(
        new Set(
            medicines.value
                .map((m) => m.category?.trim())
                .filter(Boolean)
        )
    ).sort((a, b) => a.localeCompare(b));
});

const filteredMedicines = computed(() => {
    let filtered = medicines.value;

    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(m => (m.category || '') === selectedCategory.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(m => {
            const haystack = [
                m.name,
                m.category || '',
                m.description || '',
                m.unit || ''
            ].join(' ').toLowerCase();
            return haystack.includes(query);
        });
    }

    return filtered;
});

function addToCart(medicine) {
    let quantity = cartQuantities.value[medicine.id] || 1;
    quantity = Math.max(1, Math.min(quantity, medicine.stock_quantity || 1));
    cartQuantities.value[medicine.id] = quantity; // Update input field

    const cart = window.cartStore;
    if (!cart) {
        notifyError('Cart store not available.');
        return;
    }

    cart.addItem({
        medicine: {
            id: medicine.id,
            name: medicine.name,
            price: medicine.price,
            unit: medicine.unit,
        },
        quantity,
    });

    notifySuccess(`Đã thêm ${quantity} ${medicine.unit || 'hộp'} ${medicine.name} vào giỏ hàng.`);
}

async function loadData() {
    try {
        const response = await callApi('/api/owner/medicines', 'GET');
        medicines.value = response.data || [];
        
        // Initialize cart quantities to 1
        medicines.value.forEach(m => {
            cartQuantities.value[m.id] = 1;
        });
    } catch (error) {
        handleApiError(error);
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
.medicine-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.medicine-card:hover {
  transform: translateY(-2px);
}
</style>
