<x-layout.app
    title="My Cart | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-cart"
    :showSidebar="true"
>
    <section class="rounded-3xl border border-[#DDE1E6] bg-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">My Cart</h1>
                <p class="mt-1 text-sm text-[#4A4A4A]">Review items and checkout with online payment.</p>
            </div>
        </div>

        <div id="owner-cart-root" class="mt-6"></div>
    </section>
</x-layout.app>
