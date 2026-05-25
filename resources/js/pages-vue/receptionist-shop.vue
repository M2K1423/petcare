<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { callApi } from '../auth/http';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError } = useNotification();

type Medicine = {
    id: number;
    name: string;
    category?: string | null;
    unit?: string | null;
    stock_quantity: number;
    price: number | string;
    description?: string | null;
    expiration_date?: string | null;
    image_url?: string | null;
};

type CartItem = {
    medicine: Medicine;
    quantity: number;
};

const medicines = ref<Medicine[]>([]);
const searchTerm = ref('');
const categoryFilter = ref('all');
const isLoading = ref(true);

const cart = ref<CartItem[]>([]);
const showCheckoutModal = ref(false);

const searchCustomerTerm = ref('');
const customers = ref<any[]>([]);
const selectedCustomer = ref<any>(null);
const selectedPetId = ref<number | null>(null);
const orderNotes = ref('');
const isSubmitting = ref(false);

// Format currency
const formatCurrency = (value: number | string) => {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
};

const fetchMedicines = async () => {
    isLoading.value = true;
    try {
        const response = await callApi<{ data: Medicine[] }>('/api/receptionist/medicines', 'GET');
        medicines.value = response.data || [];
    } catch (error) {
        notifyError(error, 'Lỗi tải danh sách thuốc');
    } finally {
        isLoading.value = false;
    }
};

const filteredMedicines = computed(() => {
    return medicines.value.filter(medicine => {
        const matchSearch = medicine.name.toLowerCase().includes(searchTerm.value.toLowerCase()) || 
                            (medicine.category && medicine.category.toLowerCase().includes(searchTerm.value.toLowerCase()));
        const matchCategory = categoryFilter.value === 'all' || medicine.category === categoryFilter.value;
        return matchSearch && matchCategory;
    });
});

const categories = computed(() => {
    const cats = new Set(medicines.value.map(m => m.category).filter(c => !!c));
    return Array.from(cats).sort();
});

const addToCart = (medicine: Medicine) => {
    const existing = cart.value.find(item => item.medicine.id === medicine.id);
    if (existing) {
        if (existing.quantity < medicine.stock_quantity) {
            existing.quantity++;
            notifySuccess(`Đã tăng số lượng ${medicine.name}`);
        } else {
            notifyError(new Error('Vượt quá tồn kho'), 'Hết hàng');
        }
    } else {
        if (medicine.stock_quantity > 0) {
            cart.value.push({ medicine, quantity: 1 });
            notifySuccess(`Đã thêm ${medicine.name} vào giỏ`);
        }
    }
};

const removeFromCart = (index: number) => {
    cart.value.splice(index, 1);
};

const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => total + (Number(item.medicine.price) * item.quantity), 0);
});

// Checkout Flow
const openCheckout = () => {
    if (cart.value.length === 0) return;
    showCheckoutModal.value = true;
    searchCustomerTerm.value = '';
    customers.value = [];
    selectedCustomer.value = null;
    selectedPetId.value = null;
    orderNotes.value = '';
};

const closeCheckout = () => {
    showCheckoutModal.value = false;
};

let searchTimeout: any = null;
const handleCustomerSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        if (!searchCustomerTerm.value || searchCustomerTerm.value.length < 2) {
            customers.value = [];
            return;
        }
        try {
            const res = await callApi<any>(`/api/receptionist/customers/search?q=${encodeURIComponent(searchCustomerTerm.value)}`, 'GET');
            customers.value = res.data || [];
        } catch (error) {
            console.error('Lỗi tìm KH:', error);
        }
    }, 500);
};

const selectCustomer = (customer: any) => {
    selectedCustomer.value = customer;
    if (customer.pets && customer.pets.length > 0) {
        selectedPetId.value = customer.pets[0].id;
    } else {
        selectedPetId.value = null;
    }
};

const submitOrder = async () => {
    if (!selectedCustomer.value || !selectedPetId.value) {
        alert('Vui lòng chọn khách hàng và thú cưng!');
        return;
    }

    isSubmitting.value = true;
    try {
        const payload = {
            owner_id: selectedCustomer.value.id,
            pet_id: selectedPetId.value,
            notes: orderNotes.value,
            items: cart.value.map(item => ({
                medicine_id: item.medicine.id,
                quantity: item.quantity
            }))
        };

        const res = await callApi<any>('/api/receptionist/medicine-orders', 'POST', payload);
        notifySuccess('Đã tạo đơn thuốc thành công!');
        cart.value = [];
        closeCheckout();
        fetchMedicines(); // Cập nhật lại tồn kho
        
        // Redirect to billing to collect payment
        setTimeout(() => {
            window.location.href = '/receptionist/billing';
        }, 1500);

    } catch (error) {
        notifyError(error, 'Lỗi tạo đơn hàng');
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    fetchMedicines();
});
</script>

<template>
    <div class="grid h-[calc(100vh-64px)] grid-cols-1 lg:grid-cols-3 gap-6 p-6">
        
        <!-- Left: Product List -->
        <div class="lg:col-span-2 flex flex-col h-full bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex gap-4 items-center shrink-0">
                <div class="flex-1 relative">
                    <input v-model="searchTerm" type="text" placeholder="Tìm kiếm thuốc..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#1D4ED8] focus:border-[#1D4ED8] text-sm">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <select v-model="categoryFilter" class="w-48 py-2.5 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#1D4ED8] text-sm bg-white">
                    <option value="all">Tất cả danh mục</option>
                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 bg-slate-50/50">
                <div v-if="isLoading" class="flex justify-center items-center h-40">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#1D4ED8]"></div>
                </div>
                
                <div v-else-if="filteredMedicines.length === 0" class="text-center text-slate-500 py-10">
                    Không tìm thấy sản phẩm nào.
                </div>
                
                <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div v-for="med in filteredMedicines" :key="med.id" 
                         class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col hover:border-[#1D4ED8] hover:shadow-md transition cursor-pointer group"
                         @click="addToCart(med)">
                        <div class="aspect-square bg-slate-100 rounded-xl mb-3 overflow-hidden flex items-center justify-center relative">
                            <span v-if="med.stock_quantity <= 0" class="absolute inset-0 bg-white/70 flex items-center justify-center font-bold text-red-500 text-sm z-10 backdrop-blur-[1px]">Hết hàng</span>
                            <img :src="med.image_url || `https://placehold.co/300x300/F8FAFC/94A3B8?text=${encodeURIComponent(med.name)}`" :alt="med.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        </div>
                        <h3 class="font-semibold text-slate-800 text-sm line-clamp-2 mb-1 group-hover:text-[#1D4ED8]">{{ med.name }}</h3>
                        <div class="flex justify-between items-center mt-auto pt-2">
                            <span class="text-[#1D4ED8] font-bold text-sm">{{ formatCurrency(med.price) }}</span>
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-md font-medium">Kho: {{ med.stock_quantity }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Cart -->
        <div class="h-full bg-white rounded-3xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div class="p-4 bg-[#1D4ED8] text-white shrink-0">
                <h2 class="font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-shopping-cart"></i> Giỏ Hàng Tại Quầy
                </h2>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-400">
                    <i class="fas fa-box-open text-4xl mb-3"></i>
                    <p>Chưa có sản phẩm</p>
                </div>
                
                <div v-for="(item, index) in cart" :key="item.medicine.id" class="flex gap-3 items-center border border-slate-100 p-3 rounded-xl bg-slate-50">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-slate-800 truncate">{{ item.medicine.name }}</p>
                        <p class="text-[#1D4ED8] font-semibold text-xs">{{ formatCurrency(item.medicine.price) }}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg shrink-0">
                        <button @click="item.quantity > 1 ? item.quantity-- : removeFromCart(index)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-l-lg">-</button>
                        <span class="text-sm font-medium w-6 text-center">{{ item.quantity }}</span>
                        <button @click="item.quantity < item.medicine.stock_quantity ? item.quantity++ : null" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-r-lg" :class="{'opacity-50': item.quantity >= item.medicine.stock_quantity}">+</button>
                    </div>
                    <button @click="removeFromCart(index)" class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg shrink-0">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-slate-600">Tổng tiền:</span>
                    <span class="font-bold text-xl text-[#1D4ED8]">{{ formatCurrency(cartTotal) }}</span>
                </div>
                <button @click="openCheckout" :disabled="cart.length === 0" class="w-full bg-[#1D4ED8] text-white font-bold py-3.5 rounded-xl hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    Tiến hành Thanh toán <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div v-if="showCheckoutModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-full">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold text-slate-800">Xác nhận tạo đơn thuốc</h3>
                <button @click="closeCheckout" class="text-slate-400 hover:text-slate-600 bg-slate-100 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <!-- Select Customer -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">1. Khách hàng (SĐT / Tên)</label>
                    <div class="relative">
                        <input v-model="searchCustomerTerm" @input="handleCustomerSearch" type="text" placeholder="Nhập để tìm kiếm khách hàng..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1D4ED8] focus:border-[#1D4ED8]">
                    </div>
                    
                    <div v-if="customers.length > 0 && !selectedCustomer" class="mt-2 border border-slate-200 rounded-xl overflow-hidden shadow-sm max-h-48 overflow-y-auto">
                        <div v-for="cust in customers" :key="cust.id" @click="selectCustomer(cust)" class="p-3 border-b border-slate-100 hover:bg-blue-50 cursor-pointer flex justify-between items-center last:border-b-0">
                            <div>
                                <p class="font-semibold text-slate-800 text-sm">{{ cust.name }}</p>
                                <p class="text-xs text-slate-500">{{ cust.phone }}</p>
                            </div>
                            <span class="text-xs bg-slate-100 px-2 py-1 rounded-md">{{ cust.pets?.length || 0 }} thú cưng</span>
                        </div>
                    </div>
                    
                    <div v-if="selectedCustomer" class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center font-bold">{{ selectedCustomer.name.charAt(0) }}</div>
                            <div>
                                <p class="font-bold text-sm text-blue-900">{{ selectedCustomer.name }}</p>
                                <p class="text-xs text-blue-600">{{ selectedCustomer.phone }}</p>
                            </div>
                        </div>
                        <button @click="selectedCustomer = null; selectedPetId = null" class="text-xs text-red-500 font-medium hover:underline">Đổi</button>
                    </div>
                    
                    <p v-if="searchCustomerTerm.length >= 2 && customers.length === 0 && !selectedCustomer" class="text-xs text-red-500 mt-2">
                        Không tìm thấy khách hàng. Vui lòng vào mục "Đăng ký khách vãng lai" để tạo trước.
                    </p>
                </div>
                
                <!-- Select Pet -->
                <div v-if="selectedCustomer">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">2. Chọn thú cưng</label>
                    <select v-model="selectedPetId" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1D4ED8]">
                        <option :value="null" disabled>-- Chọn thú cưng --</option>
                        <option v-for="pet in selectedCustomer.pets" :key="pet.id" :value="pet.id">{{ pet.name }}</option>
                    </select>
                    <p v-if="selectedCustomer.pets?.length === 0" class="text-xs text-red-500 mt-1">Khách hàng này chưa có thú cưng nào.</p>
                </div>
                
                <!-- Notes -->
                <div v-if="selectedCustomer && selectedPetId">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ghi chú đơn hàng (Tùy chọn)</label>
                    <textarea v-model="orderNotes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1D4ED8] text-sm" placeholder="Ghi chú về liều dùng hoặc hướng dẫn..."></textarea>
                </div>
                
                <!-- Order Summary inside Modal -->
                <div v-if="selectedCustomer && selectedPetId" class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-600">Tổng cộng ({{ cart.length }} món):</span>
                        <span class="text-lg font-bold text-[#1D4ED8]">{{ formatCurrency(cartTotal) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="p-5 border-t border-slate-100 bg-slate-50 flex gap-3 shrink-0">
                <button @click="closeCheckout" type="button" class="flex-1 px-4 py-2.5 rounded-xl font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition">Hủy</button>
                <button @click="submitOrder" :disabled="!selectedCustomer || !selectedPetId || isSubmitting" type="button" class="flex-1 px-4 py-2.5 rounded-xl font-bold text-white bg-[#1D4ED8] hover:bg-blue-700 transition shadow-[0_8px_16px_rgba(29,78,216,0.2)] disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                    <span v-if="isSubmitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                    <span v-else>Tạo Đơn & Thu Tiền</span>
                </button>
            </div>
        </div>
    </div>
</template>
