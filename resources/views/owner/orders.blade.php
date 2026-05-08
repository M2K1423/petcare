<x-layout.app
    title="My Orders | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-orders"
    :showSidebar="true"
>
    <section class="mt-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">My medicine orders</h1>
                <p class="mt-2 text-sm text-[#4A4A4A]">Track confirmation and payment status.</p>
            </div>
        </div>

        <div id="owner-order-history" class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
            <p>Loading orders...</p>
        </div>
    </section>
</x-layout.app>
