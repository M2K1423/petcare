<template>
  <template v-if="!isLoading">
    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">Mua thuốc cho thú cưng</h1>
                <p class="mt-2 text-sm text-[#4A4A4A]">Chọn các loại thuốc còn hàng và thêm trực tiếp từng sản phẩm vào giỏ hàng.</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-[1.2fr_0.8fr]">
            <div>
                <label for="shop-medicine-search" class="text-sm font-semibold text-[#333333]">Tìm sản phẩm</label>
                <input v-model="searchQuery" id="shop-medicine-search" type="text" placeholder="Tìm theo tên, danh mục, mô tả..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
            </div>
            <div>
                <label for="shop-category-filter" class="text-sm font-semibold text-[#333333]">Danh mục</label>
                <select v-model="selectedCategory" id="shop-category-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="all">Tất cả danh mục</option>
                    <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                </select>
            </div>
        </div>

        <div class="mt-4 text-sm text-[#4A4A4A]">Đang hiển thị {{ filteredMedicines.length }} trong số {{ medicines.length }} sản phẩm.</div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div v-if="filteredMedicines.length === 0" class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666] col-span-full">No medicines available right now.</div>
            
            <article v-for="medicine in filteredMedicines" :key="medicine.id" class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col hover:border-[#1D4ED8] hover:shadow-md transition group h-full">
                <div class="aspect-[4/3] bg-slate-100 rounded-xl mb-3 overflow-hidden flex items-center justify-center relative w-full">
                    <span v-if="medicine.stock_quantity <= 0" class="absolute inset-0 bg-white/70 flex items-center justify-center font-bold text-red-500 text-sm z-10 backdrop-blur-[1px]">Hết hàng</span>
                    <img :src="medicine.image_url || getFallbackImage(medicine)" :alt="medicine.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
                
                <div class="flex flex-col flex-1">
                    <p v-if="medicine.category" class="text-[10px] font-semibold uppercase tracking-wider text-[#5078A0] mb-1">{{ medicine.category }}</p>
                    <h3 class="font-semibold text-slate-800 text-sm line-clamp-2 mb-1 group-hover:text-[#1D4ED8]">{{ medicine.name }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-3 flex-1">{{ medicine.description ?? 'Sản phẩm có sẵn tại phòng khám.' }}</p>
                    
                    <div class="flex items-end justify-between mb-3 border-t border-slate-100 pt-3">
                        <div>
                            <span class="text-[#1D4ED8] font-bold text-base sm:text-lg block leading-tight">{{ formatCurrency(medicine.price) }}</span>
                            <span class="text-[11px] text-slate-500">ĐVT: {{ medicine.unit || 'hộp' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] bg-[#E8F3FF] text-[#2A6496] px-2 py-1 rounded font-medium whitespace-nowrap">Kho: {{ medicine.stock_quantity }}</span>
                            <div v-if="getMedicineWarning(medicine)" class="mt-1"><span class="text-[10px] text-red-500 font-medium">{{ getMedicineWarning(medicine) }}</span></div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 mt-auto">
                        <div class="w-16 sm:w-20 shrink-0">
                            <input v-model.number="cartQuantities[medicine.id]" type="number" min="1" :max="medicine.stock_quantity" class="w-full rounded-xl border border-slate-200 px-1 sm:px-2 py-2 text-center text-sm outline-none transition focus:border-[#1D4ED8] focus:ring-1 focus:ring-[#1D4ED8]" :disabled="medicine.stock_quantity <= 0">
                        </div>
                        <button @click="addToCart(medicine)" type="button" class="flex-1 rounded-xl bg-[#2A6496] px-2 py-2 text-sm font-semibold text-white transition hover:bg-[#1D4ED8] disabled:cursor-not-allowed disabled:opacity-60 flex items-center justify-center gap-1.5" :disabled="medicine.stock_quantity <= 0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span class="whitespace-nowrap">Thêm</span>
                        </button>
                    </div>
                </div>
            </article>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
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
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function getFallbackImage(medicine) {
    const label = encodeURIComponent(medicine.name);
    return `https://placehold.co/320x220/F5FAFF/2A6496?text=${label}`;
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

onMounted(() => {
    loadData();
});
</script>
