<script setup lang="ts">
import { onMounted } from 'vue';
import { callApi } from '../auth/http';
import { useNotification } from '../composables/useNotification';

const { notifySuccess, notifyError, handleApiError } = useNotification();

type Medicine = {
    id: number;
    name: string;
    category?: string | null;
    sku?: string | null;
    unit?: string | null;
    stock_quantity: number;
    price: number | string;
    expiration_date?: string | null;
    description?: string | null;
    image_url?: string | null;
};

const tableEl = document.getElementById('admin-medicine-table');
const summaryEl = document.getElementById('admin-medicine-summary');
const statusEl = document.getElementById('admin-medicine-status');
const lowStockEl = document.getElementById('admin-low-stock-count');
const expiredEl = document.getElementById('admin-expired-count');
const expiringEl = document.getElementById('admin-expiring-count');
const searchInputEl = document.getElementById('admin-medicine-search') as HTMLInputElement | null;
const stockFilterEl = document.getElementById('admin-stock-filter') as HTMLSelectElement | null;
const expiryFilterEl = document.getElementById('admin-expiry-filter') as HTMLSelectElement | null;
const filterResultEl = document.getElementById('admin-filter-result');
const filterResetEl = document.getElementById('admin-filter-reset');
const formTitleEl = document.getElementById('admin-medicine-form-title');
const formEl = document.getElementById('admin-medicine-form') as HTMLFormElement | null;
const resetButtonEl = document.getElementById('admin-medicine-reset');
const submitButtonEl = document.getElementById('admin-medicine-submit') as HTMLButtonElement | null;

const idInput = document.getElementById('medicine-id') as HTMLInputElement | null;
const nameInput = document.getElementById('medicine-name') as HTMLInputElement | null;
const categoryInput = document.getElementById('medicine-category') as HTMLInputElement | null;
const skuInput = document.getElementById('medicine-sku') as HTMLInputElement | null;
const unitInput = document.getElementById('medicine-unit') as HTMLInputElement | null;
const imageUrlInput = document.getElementById('medicine-image-url') as HTMLInputElement | null;
const priceInput = document.getElementById('medicine-price') as HTMLInputElement | null;
const stockInput = document.getElementById('medicine-stock') as HTMLInputElement | null;
const expirationInput = document.getElementById('medicine-expiration-date') as HTMLInputElement | null;
const descriptionInput = document.getElementById('medicine-description') as HTMLTextAreaElement | null;

let medicinesCache: Medicine[] = [];

function getToday(): Date {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), now.getDate());
}

function getExpiryMeta(expirationDate?: string | null): { label: string; tone: string; sort: number } {
    if (!expirationDate) {
        return {
            label: 'Không có hạn dùng',
            tone: 'bg-slate-100 text-slate-600',
            sort: 3,
        };
    }

    const today = getToday();
    const datePart = expirationDate.substring(0, 10);
    const expiry = new Date(`${datePart}T00:00:00`);
    const diffDays = Math.ceil((expiry.getTime() - today.getTime()) / 86400000);

    if (diffDays < 0) {
        return {
            label: 'Đã hết hạn',
            tone: 'bg-[#FEF2F2] text-[#B91C1C]',
            sort: 0,
        };
    }

    if (diffDays <= 30) {
        return {
            label: `Sắp hết hạn trong ${diffDays} ngày`,
            tone: 'bg-[#FFFBEB] text-[#B45309]',
            sort: 1,
        };
    }

    return {
        label: 'Còn hạn dùng',
        tone: 'bg-[#ECFDF3] text-[#027A48]',
        sort: 2,
    };
}

function getStockMeta(stockQuantity: number): { label: string; tone: string } {
    if (stockQuantity <= 0) {
        return {
            label: 'Hết hàng',
            tone: 'bg-[#FEF2F2] text-[#B91C1C]',
        };
    }

    if (stockQuantity <= 5) {
        return {
            label: 'Sắp hết hàng',
            tone: 'bg-[#FFF7ED] text-[#C2410C]',
        };
    }

    return {
        label: 'Còn hàng',
        tone: 'bg-[#ECFDF3] text-[#027A48]',
    };
}

function formatCurrency(value: number | string): string {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function formatDate(dateStr?: string | null): string {
    if (!dateStr) return 'Chưa cập nhật';
    const datePart = dateStr.substring(0, 10);
    const parts = datePart.split('-');
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
}

function setStatus(message: string, tone: 'success' | 'error'): void {
    if (tone === 'success') {
        notifySuccess(message);
    } else {
        notifyError(message);
    }
    // Vẫn giữ UI cũ nếu muốn, hoặc bỏ. Hiện tại mình bỏ UI cũ (statusEl) để dồn về Toast
}

function clearStatus(): void {
    statusEl?.classList.add('hidden');
}

function resetForm(): void {
    if (formEl) formEl.reset();
    if (idInput) idInput.value = '';
    if (formTitleEl) formTitleEl.textContent = 'Thêm thuốc';
    clearStatus();
}

function fillForm(medicine: Medicine): void {
    if (idInput) idInput.value = String(medicine.id);
    if (nameInput) nameInput.value = medicine.name ?? '';
    if (categoryInput) categoryInput.value = medicine.category ?? '';
    if (skuInput) skuInput.value = medicine.sku ?? '';
    if (unitInput) unitInput.value = medicine.unit ?? '';
    if (imageUrlInput) imageUrlInput.value = medicine.image_url ?? '';
    if (priceInput) priceInput.value = String(Number(medicine.price || 0));
    if (stockInput) stockInput.value = String(medicine.stock_quantity ?? 0);
    if (expirationInput) expirationInput.value = medicine.expiration_date ? medicine.expiration_date.substring(0, 10) : '';
    if (descriptionInput) descriptionInput.value = medicine.description ?? '';
    if (formTitleEl) formTitleEl.textContent = `Cập nhật thuốc #${medicine.id}`;
}

function updateSummary(medicines: Medicine[]): void {
    if (!summaryEl) return;
    const totalProducts = medicines.length;
    const lowStock = medicines.filter((medicine) => medicine.stock_quantity <= 5).length;
    const expired = medicines.filter((medicine) => getExpiryMeta(medicine.expiration_date).sort === 0).length;
    const expiringSoon = medicines.filter((medicine) => getExpiryMeta(medicine.expiration_date).sort === 1).length;
    summaryEl.textContent = `${totalProducts} sản phẩm, ${lowStock} sắp hết hàng, ${expired} hết hạn`;
    if (lowStockEl) lowStockEl.textContent = String(lowStock);
    if (expiredEl) expiredEl.textContent = String(expired);
    if (expiringEl) expiringEl.textContent = String(expiringSoon);
}

function renderTable(medicines: Medicine[]): void {
    if (!tableEl) return;

    if (medicines.length === 0) {
        tableEl.innerHTML = '<div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">Không tìm thấy thuốc.</div>';
        return;
    }

    tableEl.innerHTML = `
        <div class="overflow-hidden rounded-3xl border border-[#DDE1E6]">
            <table class="min-w-full divide-y divide-[#E5E7EB] bg-white text-sm">
                <thead class="bg-[#F8FAFC] text-left text-xs uppercase tracking-[0.14em] text-[#64748B]">
                    <tr>
                        <th class="px-4 py-3">Thuốc / Sản phẩm</th>
                        <th class="px-4 py-3">Giá bán</th>
                        <th class="px-4 py-3">Tồn kho</th>
                        <th class="px-4 py-3">Hạn sử dụng</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    ${medicines.map((medicine) => `
                        ${(() => {
                            const stockMeta = getStockMeta(medicine.stock_quantity);
                            const expiryMeta = getExpiryMeta(medicine.expiration_date);
                            return `
                        <tr>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-[#333333]">${medicine.name}</p>
                                <p class="mt-1 text-xs text-[#64748B]">${medicine.category ? `Danh mục: ${medicine.category} | ` : ''}SKU: ${medicine.sku || 'Chưa cập nhật'}${medicine.unit ? ` | Đơn vị: ${medicine.unit}` : ''}</p>
                                <p class="mt-1 text-xs text-[#4A4A4A]">${medicine.description || 'Không có mô tả.'}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold ${stockMeta.tone}">${stockMeta.label}</span>
                                    <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold ${expiryMeta.tone}">${expiryMeta.label}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-semibold text-[#2A6496]">${formatCurrency(medicine.price)}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${stockMeta.tone}">${medicine.stock_quantity}</span>
                            </td>
                            <td class="px-4 py-4 text-[#4A4A4A]">${formatDate(medicine.expiration_date)}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-action="edit" data-id="${medicine.id}" class="rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-2 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Sửa</button>
                                    <button type="button" data-action="delete" data-id="${medicine.id}" class="rounded-xl border border-[#F5C2C7] bg-[#FEF3F2] px-3 py-2 text-xs font-semibold text-[#B42318] transition hover:bg-[#FDECEC]">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    `; })()}
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;

    tableEl.querySelectorAll('button[data-action="edit"]').forEach((button) => {
        button.addEventListener('click', () => {
            const id = Number((button as HTMLButtonElement).dataset.id);
            const medicine = medicinesCache.find((item) => item.id === id);
            if (medicine) fillForm(medicine);
        });
    });

    tableEl.querySelectorAll('button[data-action="delete"]').forEach((button) => {
        button.addEventListener('click', async () => {
            const id = Number((button as HTMLButtonElement).dataset.id);
            const medicine = medicinesCache.find((item) => item.id === id);
            if (!medicine || !window.confirm(`Bạn có chắc chắn muốn xóa thuốc ${medicine.name}?`)) return;

            try {
                await callApi(`/api/admin/medicines/${id}`, 'DELETE');
                notifySuccess('Đã xóa thuốc thành công.');
                await loadMedicines();
                if (idInput?.value === String(id)) resetForm();
            } catch (error) {
                notifyError((error as Error).message || 'Xóa thuốc thất bại.');
            }
        });
    });
}

function applyFilters(): void {
    const searchTerm = searchInputEl?.value.trim().toLowerCase() || '';
    const stockFilter = stockFilterEl?.value || 'all';
    const expiryFilter = expiryFilterEl?.value || 'all';

    const filtered = medicinesCache.filter((medicine) => {
        const haystack = [
            medicine.name,
            medicine.category ?? '',
            medicine.sku ?? '',
            medicine.unit ?? '',
            medicine.description ?? '',
        ].join(' ').toLowerCase();

        if (searchTerm && !haystack.includes(searchTerm)) {
            return false;
        }

        if (stockFilter === 'low' && !(medicine.stock_quantity > 0 && medicine.stock_quantity <= 5)) {
            return false;
        }

        if (stockFilter === 'out' && medicine.stock_quantity !== 0) {
            return false;
        }

        if (stockFilter === 'healthy' && medicine.stock_quantity <= 5) {
            return false;
        }

        const expiryMeta = getExpiryMeta(medicine.expiration_date).sort;

        if (expiryFilter === 'expired' && expiryMeta !== 0) {
            return false;
        }

        if (expiryFilter === 'expiring' && expiryMeta !== 1) {
            return false;
        }

        if (expiryFilter === 'safe' && expiryMeta !== 2) {
            return false;
        }

        if (expiryFilter === 'missing' && medicine.expiration_date) {
            return false;
        }

        return true;
    });

    renderTable(filtered);

    if (filterResultEl) {
        filterResultEl.textContent = `Hiển thị ${filtered.length} trên ${medicinesCache.length} sản phẩm.`;
    }
}

async function loadMedicines(): Promise<void> {
    const response = await callApi<{ data: Medicine[] }>('/api/admin/medicines', 'GET');
    medicinesCache = response.data;
    updateSummary(medicinesCache);
    applyFilters();
}

async function saveMedicine(): Promise<void> {
    if (!formEl) return;

    const payload = {
        name: nameInput?.value.trim() || '',
        category: categoryInput?.value.trim() || null,
        sku: skuInput?.value.trim() || null,
        unit: unitInput?.value.trim() || null,
        image_url: imageUrlInput?.value.trim() || null,
        price: Number(priceInput?.value || 0),
        stock_quantity: Number(stockInput?.value || 0),
        expiration_date: expirationInput?.value || null,
        description: descriptionInput?.value.trim() || null,
    };

    const editingId = idInput?.value ? Number(idInput.value) : null;

    submitButtonEl?.setAttribute('disabled', 'true');

    try {
        if (editingId) {
            await callApi(`/api/admin/medicines/${editingId}`, 'PUT', payload);
            notifySuccess('Cập nhật thuốc thành công.');
        } else {
            await callApi('/api/admin/medicines', 'POST', payload);
            notifySuccess('Thêm thuốc thành công.');
        }

        resetForm();
        await loadMedicines();
    } catch (error) {
        notifyError((error as Error).message || 'Lưu thuốc thất bại.');
    } finally {
        submitButtonEl?.removeAttribute('disabled');
    }
}

onMounted(() => {
    loadMedicines().catch((error) => {
        notifyError((error as Error).message || 'Tải danh sách thuốc thất bại.');
    });

    formEl?.addEventListener('submit', (event) => {
        event.preventDefault();
        void saveMedicine();
    });

    resetButtonEl?.addEventListener('click', resetForm);

    searchInputEl?.addEventListener('input', applyFilters);
    stockFilterEl?.addEventListener('change', applyFilters);
    expiryFilterEl?.addEventListener('change', applyFilters);
    filterResetEl?.addEventListener('click', () => {
        if (searchInputEl) searchInputEl.value = '';
        if (stockFilterEl) stockFilterEl.value = 'all';
        if (expiryFilterEl) expiryFilterEl.value = 'all';
        applyFilters();
    });
});
</script>

<template>
    <div class="hidden"></div>
</template>
