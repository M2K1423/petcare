<x-layout.app
    title="Hồ sơ sức khỏe thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-pet-health-records"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-[#333333]">Bệnh án & lịch tiêm phòng</h1>
                    <p class="mt-2 text-sm text-[#4A4A4A]">Theo dõi lịch sử khám và các mũi tiêm của thú cưng.</p>
                    <p id="pet-health-records-pet" class="mt-3 text-sm font-semibold text-[#2A6496]">Thú cưng: Đang tải...</p>
                </div>
                <a href="{{ route('owner.pets') }}" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">
                    Quay lại danh sách thú cưng
                </a>
            </div>

            <p id="pet-health-records-status" class="mt-4 text-sm text-[#4A4A4A]">Đang tải hồ sơ...</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Bệnh án</h2>
            <div id="pet-medical-records-list" class="mt-4 space-y-3 text-sm text-[#4A4A4A]"></div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Lịch tiêm phòng</h2>
            <div id="pet-vaccinations-list" class="mt-4 space-y-3 text-sm text-[#4A4A4A]"></div>
        </article>
    </section>

    <script>
        window.__OWNER_PET_HEALTH_RECORDS__ = {
            petId: @json($petId),
        };
    </script>
</x-layout.app>
