<x-layout.app
    title="Owner Overview | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-overview"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Thú cưng của bạn</p>
            <p id="overview-pets-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Tổng số hồ sơ thú cưng trong tài khoản của bạn.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Lịch hẹn</p>
            <p id="overview-appointments-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Tổng số lịch hẹn bạn đã tạo.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Lịch hẹn chờ xử lý</p>
            <p id="overview-pending-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Các lịch hẹn đang chờ xác nhận.</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Thao tác nhanh</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('owner.pets') }}" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">Thêm hoặc sửa thú cưng</a>
                <a href="{{ route('owner.appointments') }}" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Đặt lịch hẹn</a>
                <a href="{{ route('owner.shop') }}" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Mua thuốc</a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Lịch hẹn gần đây</h2>
            <div id="overview-recent-appointments" class="mt-4 space-y-2 text-sm text-[#4A4A4A]">
                <p>Đang tải...</p>
            </div>
        </article>
    </section>
</x-layout.app>
