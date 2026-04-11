<aside class="fixed inset-y-0 left-0 z-30 hidden h-screen w-72 overflow-y-auto border-r border-[#1E2C43] bg-gradient-to-b from-[#0F1E35] to-[#142845] px-4 py-6 text-[#C8D4E8] shadow-[0_20px_40px_rgba(0,0,0,0.28)] lg:block">
    <div class="border border-[#2A3D5E] bg-[#122742]/70 px-3 py-2">
        <p class="text-[11px] uppercase tracking-[0.18em] text-[#7FA6D9]">Quan ly</p>
        <p class="mt-1 text-sm font-semibold text-[#EAF2FF]">PetCare Console</p>
    </div>

    <nav class="mt-4 space-y-1 text-sm">
        <a href="{{ route('owner.overview') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.overview') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
            <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M3 12h18"></path>
                    <path d="M3 6h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
            </span>
            <span>Tong quan</span>
        </a>

        <a href="{{ route('owner.profile') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.profile') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
            <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 20c1.8-3.2 4.5-5 8-5s6.2 1.8 8 5"></path>
                </svg>
            </span>
            <span>Profile</span>
        </a>

        <a href="{{ route('owner.pets') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.pets*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
            <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M5 3v18"></path>
                    <path d="M19 3v18"></path>
                    <path d="M5 7h14"></path>
                    <path d="M5 12h14"></path>
                    <path d="M5 17h14"></path>
                </svg>
            </span>
            <span>Quan ly thu cung</span>
        </a>

        <a href="{{ route('owner.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.appointments') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
            <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <span>Lich hen kham</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 text-[#B8C7DE] transition hover:bg-[#1A304E] hover:text-[#F5FAFF]">
            <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                </svg>
            </span>
            <span>Thong bao</span>
        </a>
    </nav>
</aside>
