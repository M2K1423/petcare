<x-layout.app
    title="C&#7917;a h&#224;ng th&#250; c&#432;ng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-shop"
    :showSidebar="true"
>
    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">Mua thu&#7889;c cho th&#250; c&#432;ng</h1>
                <p class="mt-2 text-sm text-[#4A4A4A]">Ch&#7885;n c&#225;c lo&#7841;i thu&#7889;c c&#242;n h&#224;ng v&#224; th&#234;m tr&#7921;c ti&#7871;p t&#7915;ng s&#7843;n ph&#7849;m v&#224;o gi&#7887; h&#224;ng.</p>
            </div>
        </div>

        <div id="shop-status" class="mt-4 hidden rounded-2xl px-4 py-3 text-sm"></div>

        <div class="mt-6 grid gap-4 md:grid-cols-[1.2fr_0.8fr]">
            <div>
                <label for="shop-medicine-search" class="text-sm font-semibold text-[#333333]">T&#236;m s&#7843;n ph&#7849;m</label>
                <input id="shop-medicine-search" type="text" placeholder="T&#236;m theo t&#234;n, danh m&#7909;c, m&#244; t&#7843;..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
            </div>
            <div>
                <label for="shop-category-filter" class="text-sm font-semibold text-[#333333]">Danh m&#7909;c</label>
                <select id="shop-category-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="all">T&#7845;t c&#7843; danh m&#7909;c</option>
                </select>
            </div>
        </div>

        <div id="shop-filter-result" class="mt-4 text-sm text-[#4A4A4A]">&#272;ang hi&#7875;n th&#7883; t&#7845;t c&#7843; s&#7843;n ph&#7849;m.</div>

        <div id="shop-medicine-list" class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">&#272;ang t&#7843;i thu&#7889;c...</div>
        </div>
    </section>
</x-layout.app>
