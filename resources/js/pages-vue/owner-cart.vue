<template>
  <template v-if="!isLoading">
    <section class="rounded-3xl border border-[#DDE1E6] bg-white p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">Giỏ hàng của tôi</h1>
                <p class="mt-1 text-sm text-[#4A4A4A]">Xem lại sản phẩm và thanh toán trực tuyến.</p>
            </div>
        </div>

        <div class="mt-6">
            <div v-if="items.length === 0" class="p-6 text-center text-sm text-[#4A4A4A]">Giỏ hàng của bạn đang trống.</div>
            
            <div v-else class="space-y-4">
                <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
                    <div class="space-y-3">
                        <div v-for="item in items" :key="item.medicine.id" class="rounded-2xl border p-3 flex items-center justify-between bg-[#F9FBFD]">
                            <div>
                                <p class="font-semibold text-[#333333]">{{ item.medicine.name }}</p>
                                <p class="text-xs text-[#64748B]">{{ formatCurrency(item.medicine.price) }} {{ item.medicine.unit ? '/ ' + item.medicine.unit : '' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" min="1" v-model.number="item.quantity" @change="updateQuantity(item)" class="w-20 rounded-xl border border-[#C1C4C9] px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                                <button @click="removeItem(item.medicine.id)" class="inline-flex items-center rounded-lg bg-[#FEF3F2] px-3 py-2 text-sm text-[#B42318] transition hover:bg-[#FEE4E2] gap-1">
                                    <Trash2 class="w-3.5 h-3.5" />
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <aside class="rounded-2xl border border-[#DDE1E6] p-4 bg-[#F9FBFD]">
                        <p class="text-sm font-semibold text-[#4A4A4A]">Chọn thú cưng</p>
                        <select v-model="selectedPet" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                            <option value="">-- Vui lòng chọn thú cưng --</option>
                            <option v-for="pet in pets" :key="pet.id" :value="pet.id">{{ pet.name }}</option>
                        </select>
                        
                        <p class="mt-4 text-sm font-semibold text-[#4A4A4A]">Phương thức thanh toán</p>
                        <select v-model="paymentMethod" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] px-3 py-2 text-sm outline-none focus:border-[#2A6496]">
                            <option value="vnpay">Thanh toán trực tuyến (VNPay)</option>
                            <option value="cash">Thanh toán tại phòng khám</option>
                        </select>
                        
                        <p class="mt-4 text-sm font-semibold text-[#4A4A4A]">Ghi chú</p>
                        <textarea v-model="notes" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] px-3 py-2 text-sm outline-none focus:border-[#2A6496]"></textarea>
                        
                        <div class="mt-6 flex items-center justify-between border-t border-[#DDE1E6] pt-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Tổng cộng</p>
                                <p class="text-2xl font-extrabold text-[#2A6496]">{{ formatCurrency(total) }}</p>
                            </div>
                            <div>
                                <button @click="checkoutFlow" :disabled="isCheckingOut" class="inline-flex items-center rounded-xl bg-[#2A6496] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#235780] disabled:opacity-50 gap-1.5">
                                    <CreditCard v-if="!isCheckingOut" class="w-4 h-4" />
                                    {{ isCheckingOut ? 'Đang xử lý...' : 'Thanh toán' }}
                                </button>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Trash2, CreditCard } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

const isLoading = ref(true);
const isCheckingOut = ref(false);
const items = ref([]);
const total = ref(0);
const pets = ref([]);
const selectedPet = ref('');
const paymentMethod = ref('vnpay');
const notes = ref('');

function formatCurrency(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function updateStoreState() {
    const cart = window.cartStore;
    if (cart) {
        items.value = cart.getItems();
        total.value = cart.getTotal();
    }
}

function updateQuantity(item) {
    const val = Math.max(1, Number(item.quantity) || 1);
    const cart = window.cartStore;
    if (cart) {
        cart.setItemQty(item.medicine.id, val);
        updateStoreState();
    }
}

function removeItem(id) {
    const cart = window.cartStore;
    if (cart) {
        cart.removeItem(id);
        updateStoreState();
        notifySuccess('Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}

async function loadPets() {
    try {
        const resp = await callApi('/api/owner/pets', 'GET');
        pets.value = resp.data || [];
    } catch (e) {
        pets.value = [];
    }
}

async function checkoutFlow() {
    if (!selectedPet.value) {
        notifyError('Vui lòng chọn thú cưng trước khi thanh toán.');
        return;
    }

    if (items.value.length === 0) {
        notifyError('Giỏ hàng của bạn đang trống.');
        return;
    }

    isCheckingOut.value = true;
    
    const payload = {
        pet_id: selectedPet.value,
        notes: notes.value,
        items: items.value.map((i) => ({ medicine_id: i.medicine.id, quantity: i.quantity })),
    };

    try {
        const created = await callApi('/api/owner/medicine-orders', 'POST', payload);
        const order = created.data;
        
        // immediately attempt payment (owner flow)
        const payResp = await callApi(`/api/owner/medicine-orders/${order.id}/pay`, 'PATCH', { 
            payment_method: paymentMethod.value, 
            notes: notes.value 
        });
        
        if (payResp?.payment_url) {
            window.location.href = payResp.payment_url;
            return;
        }

        // otherwise clear cart and show success
        const cart = window.cartStore;
        if (cart) cart.clear();
        items.value = [];
        total.value = 0;
        
        notifySuccess('Đã tạo đơn hàng thành công.');
        window.location.href = '/owner/orders'; // Redirect to orders page
    } catch (err) {
        handleApiError(err);
    } finally {
        isCheckingOut.value = false;
    }
}

onMounted(async () => {
    updateStoreState();
    await loadPets();
    isLoading.value = false;
});
</script>
