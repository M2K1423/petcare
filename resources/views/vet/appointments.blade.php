<x-layout.app
    title="Vet Appointments | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="vet-appointments"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Vet Appointments</h1>
            <p class="text-sm text-[#4A4A4A]">Review assigned cases and open each appointment to save the medical record.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="vet-date-filter" class="text-sm font-semibold text-[#333333]">Date</label>
                <input id="vet-date-filter" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
            </div>
            <div>
                <label for="vet-status-filter" class="text-sm font-semibold text-[#333333]">Status</label>
                <select id="vet-status-filter" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    <option value="">All statuses</option>
                    <option value="awaiting_exam">Cho kham</option>
                    <option value="examining">Dang kham</option>
                    <option value="awaiting_lab">Cho xet nghiem</option>
                    <option value="treating">Dang dieu tri</option>
                    <option value="completed">Hoan thanh</option>
                    <option value="follow_up">Tai kham</option>
                </select>
            </div>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#D7E6F5] bg-[#F5FAFF] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#5078A0]">Today</p>
            <p id="vet-today-count" class="mt-1 text-2xl font-extrabold text-[#2A6496]">0</p>
        </div>
        <div class="rounded-2xl border border-[#FDE68A] bg-[#FFFBEB] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#B45309]">Waiting Exam</p>
            <p id="vet-confirmed-count" class="mt-1 text-2xl font-extrabold text-[#92400E]">0</p>
        </div>
        <div class="rounded-2xl border border-[#C7EAD8] bg-[#ECFDF3] px-4 py-4">
            <p class="text-xs uppercase tracking-[0.14em] text-[#027A48]">Completed</p>
            <p id="vet-completed-count" class="mt-1 text-2xl font-extrabold text-[#027A48]">0</p>
        </div>
    </section>

    <section class="mt-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
        <div id="vet-appointments-list" class="space-y-4 text-sm text-[#4A4A4A]">
            <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">Loading appointments...</div>
        </div>
    </section>
</x-layout.app>
