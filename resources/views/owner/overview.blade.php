<x-layout.app
    title="Owner Overview | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/pages/owner-overview.ts']"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Your pets</p>
            <p id="overview-pets-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Total pet profiles in your account.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Appointments</p>
            <p id="overview-appointments-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Total appointments you created.</p>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Pending appointments</p>
            <p id="overview-pending-count" class="mt-3 text-4xl font-extrabold text-[#2A6496]">0</p>
            <p class="mt-2 text-sm text-[#4A4A4A]">Appointments waiting for confirmation.</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Quick actions</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('owner.pets') }}" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">Add or Edit Pet</a>
                <a href="{{ route('owner.appointments') }}" class="inline-flex items-center justify-center rounded-xl border border-[#C1C4C9] bg-[#F1F3F5] px-4 py-2 text-sm font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Book Appointment</a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Recent appointments</h2>
            <div id="overview-recent-appointments" class="mt-4 space-y-2 text-sm text-[#4A4A4A]">
                <p>Loading...</p>
            </div>
        </article>
    </section>
</x-layout.app>
