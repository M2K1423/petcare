<x-layout.app
    title="Cửa hàng thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-shop"
    :showSidebar="true"
>
    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">Mua thuốc cho thú cưng</h1>
                <p class="mt-2 text-sm text-[#4A4A4A]">Chọn các loại thuốc còn hàng và thêm trực tiếp từng sản phẩm vào giỏ hàng.</p>
            </div>
        </div>

        <div id="shop-status" class="mt-4 hidden rounded-2xl px-4 py-3 text-sm"></div>

        <div class="mt-6">
            <label for="shop-pet-select" class="text-sm font-semibold text-[#333333]">Chọn thú cưng</label>
            <select id="shop-pet-select" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                <option value="">Chọn một thú cưng</option>
            </select>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-[1.2fr_0.8fr]">
            <div>
                <label for="shop-medicine-search" class="text-sm font-semibold text-[#333333]">Tìm sản phẩm</label>
                <input id="shop-medicine-search" type="text" placeholder="Tìm theo tên, danh mục, mô tả..." class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
            </div>
            <div>
                <label for="shop-category-filter" class="text-sm font-semibold text-[#333333]">Danh mục</label>
                <select id="shop-category-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="all">Tất cả danh mục</option>
                </select>
            </div>
        </div>

        <div id="shop-filter-result" class="mt-4 text-sm text-[#4A4A4A]">Đang hiển thị tất cả sản phẩm.</div>

        <div id="shop-medicine-list" class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#666666]">Đang tải thuốc...</div>
        </div>
    </section>
</x-layout.app>
