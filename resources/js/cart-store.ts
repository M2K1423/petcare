type CartItem = {
    medicine: { id: number; name: string; price: number | string; unit?: string | null };
    quantity: number;
};

const STORAGE_KEY = 'petcare_owner_cart_v1';

function read(): CartItem[] {
    try {
        const raw = localStorage.getItem(STORAGE_KEY) || '[]';
        return JSON.parse(raw);
    } catch (e) {
        return [];
    }
}

function write(items: CartItem[]) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    window.dispatchEvent(new CustomEvent('petcare-cart-changed'));
}

const cart = {
    addItem(item: CartItem) {
        const items = read();
        const existing = items.find((i) => i.medicine.id === item.medicine.id);
        if (existing) {
            existing.quantity = Math.max(0, existing.quantity + item.quantity);
        } else {
            items.push({ medicine: item.medicine, quantity: Math.max(0, item.quantity) });
        }
        write(items);
        // try to sync to server when logged in
        syncToServer().catch(() => {});
    },
    setItemQty(medicineId: number, qty: number) {
        const items = read();
        const existing = items.find((i) => i.medicine.id === medicineId);
        if (existing) {
            existing.quantity = Math.max(0, qty);
            write(items);
            syncToServer().catch(() => {});
        }
    },
    removeItem(medicineId: number) {
        const items = read().filter((i) => i.medicine.id !== medicineId);
        write(items);
        syncToServer().catch(() => {});
    },
    clear() {
        write([]);
        syncToServer().catch(() => {});
    },
    getItems(): CartItem[] {
        return read();
    },
    getTotal(): number {
        return read().reduce((s, i) => s + Number(i.medicine.price) * i.quantity, 0);
    },
};

// expose simple API on window for legacy pages
(window as any).cartStore = cart;

async function syncToServer(): Promise<void> {
    try {
        const token = localStorage.getItem('petcare_sanctum_token');
        if (!token) return;
        const items = read();

        // Send full list to server (replace semantics)
        await fetch('/api/owner/cart/items', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                // server accepts single item upsert; we send each item sequentially
            }),
            credentials: 'same-origin',
        });

        // Better: send items one by one to upsert
        for (const it of items) {
            await fetch('/api/owner/cart/items', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ medicine_id: it.medicine.id, quantity: it.quantity, medicine: it.medicine }),
                credentials: 'same-origin',
            });
        }
    } catch (e) {
        // ignore sync errors; keep local copy
    }
}

export default cart;
