<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';

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

const medicineListEl = document.getElementById('shop-medicine-list');
const searchInputEl = document.getElementById('shop-medicine-search') as HTMLInputElement | null;
const categoryFilterEl = document.getElementById('shop-category-filter') as HTMLSelectElement | null;
const filterResultEl = document.getElementById('shop-filter-result');
const statusEl = document.getElementById('shop-status');

let medicinesCache: Medicine[] = [];
let statusHideTimer: ReturnType<typeof setTimeout> | null = null;
let statusClearTimer: ReturnType<typeof setTimeout> | null = null;

function formatCurrency(value: number | string): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function setStatus(message: string, tone: 'success' | 'error'): void {
    if (!statusEl) return;

    if (statusHideTimer) {
        clearTimeout(statusHideTimer);
        statusHideTimer = null;
    }

    if (statusClearTimer) {
        clearTimeout(statusClearTimer);
        statusClearTimer = null;
    }

    statusEl.textContent = message;
    statusEl.className = `fixed right-6 top-24 z-50 max-w-sm rounded-2xl border px-4 py-3 text-sm shadow-[0_16px_40px_rgba(0,0,0,0.12)] transition-all duration-300 ease-out will-change-transform ${tone === 'success' ? 'border-[#ABEFC6] bg-[#ECFDF3] text-[#027A48]' : 'border-[#FECACA] bg-[#FEF3F2] text-[#B42318]'}`;
    statusEl.classList.remove('hidden');
    statusEl.classList.add('opacity-0', 'translate-y-2', 'scale-95', 'pointer-events-none');

    requestAnimationFrame(() => {
        if (!statusEl) return;
        statusEl.classList.remove('opacity-0', 'translate-y-2', 'scale-95', 'pointer-events-none');
        statusEl.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
    });

    if (tone === 'success') {
        statusHideTimer = setTimeout(() => {
            if (!statusEl) return;
            statusEl.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
            statusEl.classList.add('opacity-0', 'translate-y-2', 'scale-95', 'pointer-events-none');

            statusClearTimer = setTimeout(() => {
                if (!statusEl) return;
                statusEl.classList.add('hidden');
                statusHideTimer = null;
                statusClearTimer = null;
            }, 300);
        }, 1800);
    }
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

function addToCart(medicine: Medicine, input: HTMLInputElement): void {
    const quantity = Math.max(1, Math.min(Number(input.value || 1), medicine.stock_quantity || 1));
    input.value = String(quantity);

    const cart = (window as any).cartStore;
    if (!cart) {
        setStatus('Cart store not available.', 'error');
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

    setStatus(`${medicine.name} added to cart.`, 'success');
}

function renderMedicines(medicines: Medicine[]): void {
    if (!medicineListEl) return;

    if (medicines.length === 0) {
        medicineListEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">No medicines available right now.</div>';
        return;
    }

    medicineListEl.innerHTML = medicines.map((medicine) => `
        <article class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col hover:border-[#1D4ED8] hover:shadow-md transition group h-full">
            <div class="aspect-[4/3] bg-slate-100 rounded-xl mb-3 overflow-hidden flex items-center justify-center relative w-full">
                ${medicine.stock_quantity <= 0 ? `<span class="absolute inset-0 bg-white/70 flex items-center justify-center font-bold text-red-500 text-sm z-10 backdrop-blur-[1px]">Hết hàng</span>` : ''}
                <img src="${medicine.image_url || getFallbackImage(medicine)}" alt="${medicine.name}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            </div>
            
            <div class="flex flex-col flex-1">
                ${medicine.category ? `<p class="text-[10px] font-semibold uppercase tracking-wider text-[#5078A0] mb-1">${medicine.category}</p>` : ''}
                <h3 class="font-semibold text-slate-800 text-sm line-clamp-2 mb-1 group-hover:text-[#1D4ED8]">${medicine.name}</h3>
                <p class="text-xs text-slate-500 line-clamp-2 mb-3 flex-1">${medicine.description ?? 'Sản phẩm có sẵn tại phòng khám.'}</p>
                
                <div class="flex items-end justify-between mb-3 border-t border-slate-100 pt-3">
                    <div>
                        <span class="text-[#1D4ED8] font-bold text-base sm:text-lg block leading-tight">${formatCurrency(medicine.price)}</span>
                        <span class="text-[11px] text-slate-500">ĐVT: ${medicine.unit || 'hộp'}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] bg-[#E8F3FF] text-[#2A6496] px-2 py-1 rounded font-medium whitespace-nowrap">Kho: ${medicine.stock_quantity}</span>
                        ${getMedicineWarning(medicine) ? `<div class="mt-1"><span class="text-[10px] text-red-500 font-medium">${getMedicineWarning(medicine)}</span></div>` : ''}
                    </div>
                </div>
                
                <div class="flex items-center gap-2 mt-auto">
                    <div class="w-16 sm:w-20 shrink-0">
                        <input data-medicine-qty="${medicine.id}" type="number" min="1" max="${medicine.stock_quantity}" value="1" class="w-full rounded-xl border border-slate-200 px-1 sm:px-2 py-2 text-center text-sm outline-none transition focus:border-[#1D4ED8] focus:ring-1 focus:ring-[#1D4ED8]" ${medicine.stock_quantity <= 0 ? 'disabled' : ''}>
                    </div>
                    <button data-medicine-add="${medicine.id}" type="button" class="flex-1 rounded-xl bg-[#2A6496] px-2 py-2 text-sm font-semibold text-white transition hover:bg-[#1D4ED8] disabled:cursor-not-allowed disabled:opacity-60 flex items-center justify-center gap-1.5" ${medicine.stock_quantity <= 0 ? 'disabled' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <span class="whitespace-nowrap">Thêm</span>
                    </button>
                </div>
            </div>
        </article>
    `).join('');

    medicineListEl.querySelectorAll('button[data-medicine-add]').forEach((button) => {
        button.addEventListener('click', () => {
            const id = Number((button as HTMLButtonElement).dataset.medicineAdd);
            const medicine = medicines.find((item) => item.id === id);
            if (!medicine) return;

            const qtyInput = medicineListEl.querySelector(`input[data-medicine-qty="${id}"]`) as HTMLInputElement | null;
            if (!qtyInput) return;

            addToCart(medicine, qtyInput);
        });
    });

    medicineListEl.querySelectorAll('input[data-medicine-qty]').forEach((input) => {
        input.addEventListener('input', (event) => {
            const target = event.target as HTMLInputElement;
            const medicineId = Number(target.dataset.medicineQty);
            const medicine = medicines.find((item) => item.id === medicineId);
            if (!medicine) return;

            const quantity = Math.max(1, Math.min(Number(target.value || 1), medicine.stock_quantity || 1));
            target.value = String(quantity);
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

async function loadPage(): Promise<void> {
    const response = await callApi<{ data: Medicine[] }>('/api/owner/medicines', 'GET');
    medicinesCache = response.data;
    renderCategoryOptions(medicinesCache);
    applyMedicineFilters();
}

onMounted(() => {
    loadPage().catch((error) => {
        setStatus((error as Error).message || 'Failed to load shop data.', 'error');
    });

    searchInputEl?.addEventListener('input', applyMedicineFilters);
    categoryFilterEl?.addEventListener('change', applyMedicineFilters);
});
</script>

<template>
    <div class="hidden"></div>
</template>
