<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { callApi } from '../auth/http';

type CartItem = { medicine: { id: number; name: string; price: number | string; unit?: string | null }; quantity: number };

const rootEl = document.getElementById('owner-cart-root');
const items = ref<CartItem[]>((window as any).cartStore?.getItems() || []);
const total = ref<number>((window as any).cartStore?.getTotal() || 0);
const pets = ref<any[]>([]);
const selectedPet = ref<number | null>(null);
const paymentMethod = ref<string>('vnpay');
const notes = ref<string>('');

function formatCurrency(value: number | string): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function render() {
    if (!rootEl) return;
    if (items.value.length === 0) {
        rootEl.innerHTML = '<div class="p-6 text-center text-sm text-[#4A4A4A]">Your cart is empty.</div>';
        return;
    }

    rootEl.innerHTML = `
        <div class="space-y-4">
            <div class="grid gap-4 lg:grid-cols-[1fr_320px]">
                <div class="space-y-3">
                    ${items.value.map(i => `
                        <div class="rounded-2xl border p-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-[#333333]">${i.medicine.name}</p>
                                <p class="text-xs text-[#64748B]">${formatCurrency(i.medicine.price)} ${i.medicine.unit ? '/ ' + i.medicine.unit : ''}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input data-id="${i.medicine.id}" type="number" min="0" value="${i.quantity}" class="w-20 rounded-xl border px-3 py-2 text-sm">
                                <button data-remove="${i.medicine.id}" class="rounded-lg bg-[#FEF3F2] px-3 py-2 text-sm text-[#B42318]">Remove</button>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <aside class="rounded-2xl border p-4">
                    <p class="text-sm text-[#64748B]">Select pet</p>
                    <select id="cart-pet-select" class="mt-2 w-full rounded-2xl border px-3 py-2">
                        <option value="">Choose a pet</option>
                        ${pets.value.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                    </select>
                    <p class="mt-4 text-sm text-[#64748B]">Payment method</p>
                    <select id="cart-payment-method" class="mt-2 w-full rounded-2xl border px-3 py-2">
                        <option value="vnpay">VNPay (online)</option>
                        <option value="cash">Cash (pay at clinic)</option>
                    </select>
                    <p class="mt-4 text-sm text-[#64748B]">Notes</p>
                    <textarea id="cart-notes" rows="3" class="mt-2 w-full rounded-2xl border px-3 py-2"></textarea>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-[#64748B]">Total</p>
                            <p class="text-2xl font-extrabold text-[#2A6496]">${formatCurrency(total.value)}</p>
                        </div>
                        <div>
                            <button id="cart-checkout" class="rounded-lg bg-[#0055A6] px-4 py-2 text-sm font-semibold text-white">Checkout</button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    `;

    // attach listeners
    items.value.forEach((it) => {
        const input = rootEl.querySelector(`input[data-id="${it.medicine.id}"]`) as HTMLInputElement | null;
        const remove = rootEl.querySelector(`button[data-remove="${it.medicine.id}"]`) as HTMLButtonElement | null;
        if (input) {
            input.addEventListener('change', (e) => {
                const val = Math.max(0, Number((e.target as HTMLInputElement).value || 0));
                (window as any).cartStore.setItemQty(it.medicine.id, val);
                items.value = (window as any).cartStore.getItems();
                total.value = (window as any).cartStore.getTotal();
                render();
            });
        }
        if (remove) {
            remove.addEventListener('click', () => {
                (window as any).cartStore.removeItem(it.medicine.id);
                items.value = (window as any).cartStore.getItems();
                total.value = (window as any).cartStore.getTotal();
                render();
            });
        }
    });

    const petSelect = rootEl.querySelector('#cart-pet-select') as HTMLSelectElement | null;
    const pmSelect = rootEl.querySelector('#cart-payment-method') as HTMLSelectElement | null;
    const notesEl = rootEl.querySelector('#cart-notes') as HTMLTextAreaElement | null;
    const checkout = rootEl.querySelector('#cart-checkout') as HTMLButtonElement | null;

    if (petSelect) petSelect.addEventListener('change', () => { selectedPet.value = Number(petSelect.value) || null; });
    if (pmSelect) pmSelect.addEventListener('change', () => { paymentMethod.value = pmSelect.value; });
    if (notesEl) notesEl.addEventListener('input', () => { notes.value = notesEl.value; });
    if (checkout) checkout.addEventListener('click', () => { void checkoutFlow(); });
}

async function loadPets() {
    try {
        const resp = await callApi<{ data: any[] }>('/api/owner/pets', 'GET');
        pets.value = resp.data || [];
    } catch (e) {
        pets.value = [];
    }
}

async function checkoutFlow() {
    if (!selectedPet.value) {
        // eslint-disable-next-line no-alert
        alert('Please choose a pet before checkout.');
        return;
    }

    if (items.value.length === 0) {
        alert('Your cart is empty.');
        return;
    }

    const payload = {
        pet_id: selectedPet.value,
        notes: notes.value,
        items: items.value.map((i) => ({ medicine_id: i.medicine.id, quantity: i.quantity })),
    };

    try {
        const created = await callApi<{ data: any }>('/api/owner/medicine-orders', 'POST', payload);
        const order = created.data;
        // immediately attempt payment (owner flow)
        const payResp = await callApi<any>(`/api/owner/medicine-orders/${order.id}/pay`, 'PATCH', { payment_method: paymentMethod.value, notes: notes.value });
        if (payResp?.payment_url) {
            window.location.href = payResp.payment_url;
            return;
        }

        // otherwise clear cart and show success
        (window as any).cartStore.clear();
        items.value = [];
        total.value = 0;
        render();
        alert('Order created successfully.');
    } catch (err: any) {
        alert(err?.message || 'Failed to create order.');
    }
}

onMounted(async () => {
    items.value = (window as any).cartStore?.getItems() || [];
    total.value = (window as any).cartStore?.getTotal() || 0;
    await loadPets();
    render();
});
</script>

<template>
    <div class="hidden"></div>
</template>
