<header class="sticky top-0 z-50 border-b border-[#DDE1E6] bg-gradient-to-r from-[#BFE0FF] via-[#FFFFFF] to-[#FFF7D6] shadow-[0_4px_16px_rgba(0,0,0,0.05)] lg:ml-72 lg:w-[calc(100%-18rem)]">
    <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-4 px-4 py-4 md:px-8">
        <a href="{{ route('sanctum.auth') }}" class="group inline-flex items-center gap-3">
            <span class="grid h-9 w-9 place-content-center rounded-full border border-[#C1C4C9] bg-[#FFFFFF] text-xs font-bold text-[#999999]">PAW</span>
            <span>
                <span class="block text-sm font-semibold tracking-[0.22em] text-[#333333]">PETWELL</span>
                <span class="block text-[11px] uppercase tracking-[0.18em] text-[#666666]">Clinic Portal</span>
            </span>
        </a>

        <nav class="hidden items-center gap-5 text-xs font-semibold uppercase tracking-[0.16em] md:flex">
            <a href="{{ route('owner.overview') }}" class="text-[#333333] transition hover:text-[#2A6496]">Dashboard</a>
            <a href="{{ route('owner.pets') }}" class="text-[#333333] transition hover:text-[#2A6496]">Pets</a>
            <a href="{{ route('owner.appointments') }}" class="text-[#333333] transition hover:text-[#2A6496]">Appointments</a>
            <a href="#" class="text-[#333333] transition hover:text-[#2A6496]">Vets</a>
        </nav>

        <div class="ml-auto flex w-full items-center gap-2 sm:w-auto md:gap-3">
            <label class="relative block min-w-0 flex-1 sm:w-56 md:w-64">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#999999]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="Search..."
                    class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] py-2 pl-9 pr-3 text-sm text-[#333333] placeholder:text-[#999999] outline-none transition focus:border-[#2A6496]"
                />
            </label>

            <div class="inline-flex items-center gap-2 rounded-xl border border-[#C1C4C9] bg-[#FFFFFF] px-2.5 py-2">
                <span class="text-[#C1C4C9]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20c1.8-3.2 4.5-5 8-5s6.2 1.8 8 5"></path>
                    </svg>
                </span>
                <span class="text-[#999999]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"></path>
                        <path d="M10 17a2 2 0 0 0 4 0"></path>
                    </svg>
                </span>
            </div>
        </div>
    </div>
</header>
