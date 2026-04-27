<x-layout.app
    title="Pet Shop | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-shop"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#333333]">Buy medicine for your pet</h1>
                    <p class="mt-2 text-sm text-[#4A4A4A]">Choose available medicines and send the order to reception for confirmation.</p>
                </div>
                <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-3 text-right">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Checkout total</p>
                    <p id="shop-cart-total" class="mt-1 text-2xl font-extrabold text-[#2A6496]">0 VND</p>
                </div>
            </div>

            <div id="shop-status" class="mt-4 hidden rounded-2xl px-4 py-3 text-sm"></div>

            <div class="mt-6">
                <label for="shop-pet-select" class="text-sm font-semibold text-[#333333]">Select pet</label>
                <select id="shop-pet-select" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="">Choose a pet</option>
                </select>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-[1.2fr_0.8fr]">
                <div>
                    <label for="shop-medicine-search" class="text-sm font-semibold text-[#333333]">Search products</label>
                    <input id="shop-medicine-search" type="text" placeholder="Search by name, category, description..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>
                <div>
                    <label for="shop-category-filter" class="text-sm font-semibold text-[#333333]">Category</label>
                    <select id="shop-category-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                        <option value="all">All categories</option>
                    </select>
                </div>
            </div>

            <div id="shop-filter-result" class="mt-4 text-sm text-[#4A4A4A]">Showing all products.</div>

            <div id="shop-medicine-list" class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">Loading medicines...</div>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Create order</h2>
            <p class="mt-2 text-sm text-[#4A4A4A]">Receptionist will confirm the order, create payment, then collect money at the desk.</p>

            <div id="shop-selected-items" class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
                <p>No items selected yet.</p>
            </div>

            <label for="shop-order-notes" class="mt-5 block text-sm font-semibold text-[#333333]">Notes</label>
            <textarea id="shop-order-notes" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder="Optional notes for the receptionist"></textarea>

            <button id="shop-submit-button" type="button" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl border border-[#2A6496] bg-[#2A6496] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#235780]">
                Place medicine order
            </button>
        </article>
    </section>

    <section class="mt-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-[#333333]">My medicine orders</h2>
                <p class="mt-1 text-sm text-[#4A4A4A]">Track confirmation and payment status.</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Pending</p>
                    <p id="owner-orders-pending" class="mt-1 text-xl font-extrabold text-[#2A6496]">0</p>
                </div>
                <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#B45309]">Confirmed</p>
                    <p id="owner-orders-confirmed" class="mt-1 text-xl font-extrabold text-[#92400E]">0</p>
                </div>
                <div class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] px-4 py-3">
                    <p class="text-xs uppercase tracking-[0.14em] text-[#027A48]">Paid</p>
                    <p id="owner-orders-paid" class="mt-1 text-xl font-extrabold text-[#027A48]">0</p>
                </div>
            </div>
        </div>
        <div id="owner-order-history" class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
            <p>Loading orders...</p>
        </div>
    </section>
</x-layout.app>
