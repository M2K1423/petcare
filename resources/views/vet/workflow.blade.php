<x-layout.app
    title="{{ $sectionTitle ?? 'Quy trình bác sĩ' }} | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="vet-workflow"
    :showSidebar="true"
>
    <div id="vet-workflow-root"
        data-section-key="{{ $sectionKey ?? 'dashboard' }}"
        data-section-title="{{ $sectionTitle ?? 'Vet Workflow' }}"
        data-section-description="{{ $sectionDescription ?? '' }}"
    >
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#333333]">{{ $sectionTitle ?? 'Quy trình bác sĩ' }}</h1>
                <p class="text-sm text-[#4A4A4A]">{{ $sectionDescription ?? '' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs text-[#4A4A4A]">
                <span class="rounded-full border border-[#DDE1E6] bg-white px-3 py-1">Vai trò: Bác sĩ</span>
                <span class="rounded-full border border-[#DDE1E6] bg-white px-3 py-1">12 mô-đun</span>
            </div>
        </div>

        <section id="vet-workflow-content" class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5 text-sm text-[#4A4A4A]">Đang tải mô-đun...</div>
        </section>
    </div>
</x-layout.app>
