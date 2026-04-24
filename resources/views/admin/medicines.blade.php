<x-layout.app
    title="Admin Medicines | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="admin-medicines"
    :showSidebar="true"
>
    <section class="grid gap-6 xl:grid-cols-[1.4fr_0.95fr]">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#333333]">Medicine Catalog</h1>
                    <p class="mt-2 text-sm text-[#4A4A4A]">Manage products, sale price, stock quantity, and expiration information.</p>
                </div>
                <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-3 text-right">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Inventory status</p>
                    <p id="admin-medicine-summary" class="mt-1 text-sm font-semibold text-[#2A6496]">Loading...</p>
                </div>
            </div>

            <div id="admin-medicine-status" class="mt-4 hidden rounded-2xl px-4 py-3 text-sm"></div>

            <section class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-[#F5D0A9] bg-[#FFF7ED] px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#C2410C]">Low Stock</p>
                    <p id="admin-low-stock-count" class="mt-2 text-2xl font-extrabold text-[#9A3412]">0</p>
                    <p class="mt-1 text-xs text-[#9A3412]">Products with stock quantity 5 or below.</p>
                </div>
                <div class="rounded-2xl border border-[#FECACA] bg-[#FEF2F2] px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#B91C1C]">Expired</p>
                    <p id="admin-expired-count" class="mt-2 text-2xl font-extrabold text-[#991B1B]">0</p>
                    <p class="mt-1 text-xs text-[#991B1B]">Products that should no longer be sold.</p>
                </div>
                <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#B45309]">Expiring Soon</p>
                    <p id="admin-expiring-count" class="mt-2 text-2xl font-extrabold text-[#92400E]">0</p>
                    <p class="mt-1 text-xs text-[#92400E]">Products expiring within the next 30 days.</p>
                </div>
            </section>

            <section class="mt-6 grid gap-4 lg:grid-cols-[1.3fr_0.7fr_0.7fr]">
                <div>
                    <label for="admin-medicine-search" class="text-sm font-semibold text-[#333333]">Search</label>
                    <input id="admin-medicine-search" type="text" placeholder="Search by name, SKU, unit, description..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>
                <div>
                    <label for="admin-stock-filter" class="text-sm font-semibold text-[#333333]">Stock Filter</label>
                    <select id="admin-stock-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                        <option value="all">All stock levels</option>
                        <option value="low">Low stock only</option>
                        <option value="out">Out of stock only</option>
                        <option value="healthy">Healthy stock only</option>
                    </select>
                </div>
                <div>
                    <label for="admin-expiry-filter" class="text-sm font-semibold text-[#333333]">Expiry Filter</label>
                    <select id="admin-expiry-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                        <option value="all">All expiry states</option>
                        <option value="expired">Expired only</option>
                        <option value="expiring">Expiring soon</option>
                        <option value="safe">Safe expiry</option>
                        <option value="missing">No expiry date</option>
                    </select>
                </div>
            </section>

            <div class="mt-4 flex items-center justify-between gap-4">
                <p id="admin-filter-result" class="text-sm text-[#4A4A4A]">Showing all medicines.</p>
                <button id="admin-filter-reset" type="button" class="rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-2 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">
                    Clear filters
                </button>
            </div>

            <div id="admin-medicine-table" class="mt-6 overflow-x-auto">
                <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">Loading medicines...</div>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div class="flex items-center justify-between gap-3">
                <h2 id="admin-medicine-form-title" class="text-lg font-bold text-[#333333]">Add Medicine</h2>
                <button id="admin-medicine-reset" type="button" class="rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-2 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">
                    New
                </button>
            </div>

            <form id="admin-medicine-form" class="mt-5 space-y-4">
                <input type="hidden" id="medicine-id" name="medicine_id">

                <div>
                    <label for="medicine-name" class="text-sm font-semibold text-[#333333]">Name</label>
                    <input id="medicine-name" name="name" type="text" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="medicine-sku" class="text-sm font-semibold text-[#333333]">SKU</label>
                        <input id="medicine-sku" name="sku" type="text" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                    <div>
                        <label for="medicine-unit" class="text-sm font-semibold text-[#333333]">Unit</label>
                        <input id="medicine-unit" name="unit" type="text" placeholder="box, bottle..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="medicine-price" class="text-sm font-semibold text-[#333333]">Price (VND)</label>
                        <input id="medicine-price" name="price" type="number" min="0" step="0.01" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required>
                    </div>
                    <div>
                        <label for="medicine-stock" class="text-sm font-semibold text-[#333333]">Stock Quantity</label>
                        <input id="medicine-stock" name="stock_quantity" type="number" min="0" step="1" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required>
                    </div>
                </div>

                <div>
                    <label for="medicine-expiration-date" class="text-sm font-semibold text-[#333333]">Expiration Date</label>
                    <input id="medicine-expiration-date" name="expiration_date" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>

                <div>
                    <label for="medicine-description" class="text-sm font-semibold text-[#333333]">Description</label>
                    <textarea id="medicine-description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>

                <button id="admin-medicine-submit" type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-[#2A6496] bg-[#2A6496] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#235780]">
                    Save Medicine
                </button>
            </form>
        </article>
    </section>
</x-layout.app>
