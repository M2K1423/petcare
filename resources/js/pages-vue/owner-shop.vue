<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

type Pet = { id: number; name: string };

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

type Order = {
    id: number;
    status: string;
    total_amount: number | string;
    created_at: string;
    confirmed_at?: string | null;
    paid_at?: string | null;
    notes?: string | null;
    pet?: { name: string } | null;
    confirmer?: { name: string } | null;
    items?: Array<{
        quantity: number;
        unit_price: number | string;
        line_total: number | string;
        medicine?: { name: string; unit?: string | null } | null;
    }>;
    payment?: {
        status: string;
        amount?: number | string;
        payment_method?: string | null;
        paid_at?: string | null;
        transaction_code?: string | null;
        notes?: string | null;
    } | null;
};

const selectedItems = new Map<number, { medicine: Medicine; quantity: number }>();

const medicineListEl = document.getElementById('shop-medicine-list');
const petSelectEl = document.getElementById('shop-pet-select') as HTMLSelectElement | null;
const selectedItemsEl = document.getElementById('shop-selected-items');
const cartTotalEl = document.getElementById('shop-cart-total');
const submitButtonEl = document.getElementById('shop-submit-button') as HTMLButtonElement | null;
const notesEl = document.getElementById('shop-order-notes') as HTMLTextAreaElement | null;
const searchInputEl = document.getElementById('shop-medicine-search') as HTMLInputElement | null;
const categoryFilterEl = document.getElementById('shop-category-filter') as HTMLSelectElement | null;
const filterResultEl = document.getElementById('shop-filter-result');
const statusEl = document.getElementById('shop-status');
const historyEl = document.getElementById('owner-order-history');
const pendingCountEl = document.getElementById('owner-orders-pending');
const confirmedCountEl = document.getElementById('owner-orders-confirmed');
const paidCountEl = document.getElementById('owner-orders-paid');

let medicinesCache: Medicine[] = [];

function formatCurrency(value: number | string): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function setStatus(message: string, tone: 'success' | 'error'): void {
    if (!statusEl) return;
    statusEl.textContent = message;
    statusEl.className = `mt-4 rounded-2xl px-4 py-3 text-sm ${tone === 'success' ? 'bg-[#ECFDF3] text-[#027A48]' : 'bg-[#FEF3F2] text-[#B42318]'}`;
    statusEl.classList.remove('hidden');
}

function formatDateTime(input?: string | null): string {
    if (!input) return 'N/A';
    const date = new Date(input);
    if (Number.isNaN(date.getTime())) return input;
    return date.toLocaleString();
}

function getOrderTone(status: string): string {
    if (status === 'paid') return 'bg-[#ECFDF3] text-[#027A48]';
    if (status === 'confirmed') return 'bg-[#FFFBEB] text-[#B45309]';
    if (status === 'cancelled') return 'bg-[#FEF2F2] text-[#B91C1C]';
    return 'bg-[#EFF6FF] text-[#1D4ED8]';
}

function getMedicineWarning(medicine: Medicine): string {
    if (medicine.stock_quantity <= 0) return 'Out of stock';
    if (medicine.stock_quantity <= 5) return 'Low stock';
    if (!medicine.expiration_date) return '';

    const today = new Date();
    const current = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const expiry = new Date(`${medicine.expiration_date}T00:00:00`);
    const diffDays = Math.ceil((expiry.getTime() - current.getTime()) / 86400000);

    if (diffDays < 0) return 'Expired';
    if (diffDays <= 30) return 'Expiring soon';
    return '';
}

function getFallbackImage(medicine: Medicine): string {
    const label = encodeURIComponent(medicine.name);
    return `https://placehold.co/320x220/F5FAFF/2A6496?text=${label}`;
}

function renderCategoryOptions(medicines: Medicine[]): void {
    if (!categoryFilterEl) return;

    const categories = Array.from(
        new Set(
            medicines
                .map((medicine) => medicine.category?.trim())
                .filter((category): category is string => Boolean(category)),
        ),
    ).sort((left, right) => left.localeCompare(right));

    categoryFilterEl.innerHTML = `<option value="all">All categories</option>${categories.map((category) => `<option value="${category}">${category}</option>`).join('')}`;
}

function renderCart(): void {
    if (!selectedItemsEl || !cartTotalEl) return;

    const items = Array.from(selectedItems.values()).filter((item) => item.quantity > 0);
    const total = items.reduce((sum, item) => sum + Number(item.medicine.price) * item.quantity, 0);
    cartTotalEl.textContent = formatCurrency(total);

    if (items.length === 0) {
        selectedItemsEl.innerHTML = '<p>No items selected yet.</p>';
        return;
    }

    selectedItemsEl.innerHTML = items.map((item) => `
        <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-semibold text-[#333333]">${item.medicine.name}</p>
                    <p class="text-xs text-[#4A4A4A]">${item.quantity} x ${formatCurrency(item.medicine.price)}</p>
                </div>
                <p class="font-semibold text-[#2A6496]">${formatCurrency(Number(item.medicine.price) * item.quantity)}</p>
            </div>
        </div>
    `).join('');
}

function renderPets(pets: Pet[]): void {
    if (!petSelectEl) return;
    petSelectEl.innerHTML = `<option value="">Choose a pet</option>${pets.map((pet) => `<option value="${pet.id}">${pet.name}</option>`).join('')}`;
}

function renderMedicines(medicines: Medicine[]): void {
    if (!medicineListEl) return;

    if (medicines.length === 0) {
        medicineListEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">No medicines available right now.</div>';
        return;
    }

    medicineListEl.innerHTML = medicines.map((medicine) => `
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">
            <div class="flex items-start gap-4">
                <img src="${medicine.image_url || getFallbackImage(medicine)}" alt="${medicine.name}" class="h-24 w-24 shrink-0 rounded-2xl border border-[#DDE1E6] object-cover bg-white">
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            ${medicine.category ? `<p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#5078A0]">${medicine.category}</p>` : ''}
                            <h3 class="text-base font-bold text-[#333333]">${medicine.name}</h3>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="rounded-full bg-[#E8F3FF] px-3 py-1 text-xs font-semibold text-[#2A6496]">${medicine.stock_quantity} in stock</span>
                            ${getMedicineWarning(medicine) ? `<span class="rounded-full bg-[#FFF7ED] px-3 py-1 text-[11px] font-semibold text-[#C2410C]">${getMedicineWarning(medicine)}</span>` : ''}
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-[#4A4A4A]">${medicine.description ?? 'Medicine available at the clinic.'}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-lg font-extrabold text-[#2A6496]">${formatCurrency(medicine.price)}</p>
                    <p class="text-xs text-[#4A4A4A]">${medicine.unit ? `Unit: ${medicine.unit}` : 'Unit available at reception'}</p>
                </div>
                <label class="text-sm text-[#333333]">
                    Qty
                    <input data-medicine-id="${medicine.id}" type="number" min="0" max="${medicine.stock_quantity}" value="0" class="ml-2 w-20 rounded-xl border border-[#C1C4C9] px-3 py-2 text-sm outline-none transition focus:border-[#2A6496]">
                </label>
            </div>
        </article>
    `).join('');

    medicineListEl.querySelectorAll('input[data-medicine-id]').forEach((input) => {
        input.addEventListener('input', (event) => {
            const target = event.target as HTMLInputElement;
            const medicineId = Number(target.dataset.medicineId);
            const medicine = medicines.find((item) => item.id === medicineId);
            if (!medicine) return;

            const quantity = Math.max(0, Math.min(Number(target.value || 0), medicine.stock_quantity));
            target.value = String(quantity);
            selectedItems.set(medicineId, { medicine, quantity });
            renderCart();
        });
    });
}

function applyMedicineFilters(): void {
    const searchTerm = searchInputEl?.value.trim().toLowerCase() || '';
    const selectedCategory = categoryFilterEl?.value || 'all';

    const filtered = medicinesCache.filter((medicine) => {
        const haystack = [
            medicine.name,
            medicine.category ?? '',
            medicine.description ?? '',
            medicine.unit ?? '',
        ].join(' ').toLowerCase();

        if (searchTerm && !haystack.includes(searchTerm)) {
            return false;
        }

        if (selectedCategory !== 'all' && (medicine.category ?? '') !== selectedCategory) {
            return false;
        }

        return true;
    });

    renderMedicines(filtered);

    if (filterResultEl) {
        filterResultEl.textContent = `Showing ${filtered.length} of ${medicinesCache.length} products.`;
    }
}

function renderHistory(orders: Order[]): void {
    if (!historyEl) return;

    if (pendingCountEl) pendingCountEl.textContent = String(orders.filter((order) => order.status === 'pending').length);
    if (confirmedCountEl) confirmedCountEl.textContent = String(orders.filter((order) => order.status === 'confirmed').length);
    if (paidCountEl) paidCountEl.textContent = String(orders.filter((order) => order.status === 'paid').length);

    if (orders.length === 0) {
        historyEl.innerHTML = '<p>No medicine orders yet.</p>';
        return;
    }

    historyEl.innerHTML = orders.map((order) => `
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 shadow-[0_12px_24px_rgba(0,0,0,0.03)]">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-base font-bold text-[#333333]">Order #${order.id} for ${order.pet?.name ?? 'Unknown pet'}</p>
                    <p class="mt-1 text-xs text-[#4A4A4A]">Created: ${formatDateTime(order.created_at)}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${getOrderTone(order.status)}">${order.status.toUpperCase()}</span>
                    <p class="text-lg font-extrabold text-[#2A6496]">${formatCurrency(order.total_amount)}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Order Items</p>
                    <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                        ${(order.items ?? []).map((item) => `
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#333333]">${item.medicine?.name ?? 'Medicine'}</p>
                                    <p class="text-xs text-[#64748B]">${item.quantity} x ${formatCurrency(item.unit_price)} ${item.medicine?.unit ? `/ ${item.medicine.unit}` : ''}</p>
                                </div>
                                <p class="font-semibold text-[#2A6496]">${formatCurrency(item.line_total)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="rounded-2xl border border-[#DDE1E6] bg-white px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#64748B]">Payment History</p>
                    <div class="mt-3 space-y-2 text-sm text-[#4A4A4A]">
                        <p><span class="font-semibold text-[#333333]">Payment status:</span> ${order.payment?.status?.toUpperCase() ?? 'WAITING FOR RECEPTIONIST'}</p>
                        <p><span class="font-semibold text-[#333333]">Payment method:</span> ${order.payment?.payment_method ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Amount:</span> ${order.payment?.amount ? formatCurrency(order.payment.amount) : formatCurrency(order.total_amount)}</p>
                        <p><span class="font-semibold text-[#333333]">Confirmed at:</span> ${formatDateTime(order.confirmed_at)}</p>
                        <p><span class="font-semibold text-[#333333]">Confirmed by:</span> ${order.confirmer?.name ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Paid at:</span> ${formatDateTime(order.payment?.paid_at ?? order.paid_at)}</p>
                        <p><span class="font-semibold text-[#333333]">Transaction code:</span> ${order.payment?.transaction_code ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Order notes:</span> ${order.notes ?? 'N/A'}</p>
                        <p><span class="font-semibold text-[#333333]">Payment notes:</span> ${order.payment?.notes ?? 'N/A'}</p>
                    </div>
                </div>
            </div>
        </article>
    `).join('');
}

async function loadPage(): Promise<void> {
    const [petsResponse, medicinesResponse, ordersResponse] = await Promise.all([
        callApi<{ data: Pet[] }>('/api/owner/pets', 'GET'),
        callApi<{ data: Medicine[] }>('/api/owner/medicines', 'GET'),
        callApi<{ data: Order[] }>('/api/owner/medicine-orders', 'GET'),
    ]);

    renderPets(petsResponse.data);
    medicinesCache = medicinesResponse.data;
    renderCategoryOptions(medicinesCache);
    applyMedicineFilters();
    renderHistory(ordersResponse.data);
    renderCart();
}

async function submitOrder(): Promise<void> {
    if (!petSelectEl?.value) {
        setStatus('Please choose a pet before placing the order.', 'error');
        return;
    }

    const items = Array.from(selectedItems.values())
        .filter((item) => item.quantity > 0)
        .map((item) => ({ medicine_id: item.medicine.id, quantity: item.quantity }));

    if (items.length === 0) {
        setStatus('Please choose at least one medicine.', 'error');
        return;
    }

    submitButtonEl?.setAttribute('disabled', 'true');

    try {
        await callApi('/api/owner/medicine-orders', 'POST', {
            pet_id: Number(petSelectEl.value),
            notes: notesEl?.value || '',
            items,
        });

        selectedItems.clear();
        if (notesEl) notesEl.value = '';
        medicineListEl?.querySelectorAll('input[data-medicine-id]').forEach((input) => {
            (input as HTMLInputElement).value = '0';
        });

        setStatus('Medicine order sent successfully. Receptionist can confirm it now.', 'success');
        await loadPage();
    } catch (error) {
        setStatus((error as Error).message || 'Failed to place order.', 'error');
    } finally {
        submitButtonEl?.removeAttribute('disabled');
    }
}

onMounted(() => {
    loadPage().catch((error) => {
        setStatus((error as Error).message || 'Failed to load shop data.', 'error');
    });

    submitButtonEl?.addEventListener('click', () => {
        void submitOrder();
    });

    searchInputEl?.addEventListener('input', applyMedicineFilters);
    categoryFilterEl?.addEventListener('change', applyMedicineFilters);
});
</script>

<template>
    <div class="hidden"></div>
</template>
