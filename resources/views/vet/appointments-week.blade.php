<x-layout.app
    title="Vet Weekly Schedule | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="vet-appointments-week"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Weekly Schedule</h1>
            <p class="text-sm text-[#4A4A4A]">View your assigned appointments in a weekly calendar layout.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button id="vet-week-prev" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">
                Previous Week
            </button>
            <button id="vet-week-current" type="button" class="rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-white hover:bg-[#235780]">
                This Week
            </button>
            <button id="vet-week-next" type="button" class="rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] hover:border-[#2A6496] hover:text-[#2A6496]">
                Next Week
            </button>
        </div>
    </div>

    <section class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Week Range</p>
            <p id="vet-week-range" class="mt-1 text-sm font-bold text-[#2A6496]">-</p>
        </div>
        <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#B45309]">Total Appointments</p>
            <p id="vet-week-total" class="mt-1 text-2xl font-extrabold text-[#92400E]">0</p>
        </div>
        <div class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#027A48]">Completed</p>
            <p id="vet-week-completed" class="mt-1 text-2xl font-extrabold text-[#027A48]">0</p>
        </div>
    </section>

    <section class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-4 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div id="vet-week-grid" class="grid gap-3 md:grid-cols-2 xl:grid-cols-7">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-4 text-sm text-[#4A4A4A]">Loading weekly schedule...</div>
        </div>
    </section>
</x-layout.app>
