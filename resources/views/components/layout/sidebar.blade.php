<aside class="fixed inset-y-0 left-0 z-30 hidden h-screen w-72 overflow-y-auto border-r border-[#1E2C43] bg-gradient-to-b from-[#0F1E35] to-[#142845] px-4 py-6 text-[#C8D4E8] shadow-[0_20px_40px_rgba(0,0,0,0.28)] lg:block">
    <div class="border border-[#2A3D5E] bg-[#122742]/70 px-3 py-2">
        <p class="text-[11px] uppercase tracking-[0.18em] text-[#7FA6D9]">Quan ly</p>
        <p class="mt-1 text-sm font-semibold text-[#EAF2FF]">PetCare Console</p>
    </div>

    <nav class="mt-4 space-y-1 text-sm" id="sidebar-nav-container">
        
        <!-- DÀNH CHO CHỦ THÚ CƯNG -->
        <div id="nav-owner" class="hidden">
            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">📝 Dành cho Chủ thú cưng</div>
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

            <a href="{{ route('owner.shop') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.shop') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M6 6h15l-1.5 9h-12z"></path>
                        <path d="M6 6 5 3H3"></path>
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="18" cy="20" r="1"></circle>
                    </svg>
                </span>
                <span>Mua thuoc</span>
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
        </div>
        
        <!-- DÀNH CHO LỄ TÂN -->
        <div id="nav-receptionist" class="hidden">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">🏥 Dành cho Lễ tân</div>
            
            <a href="{{ route('receptionist.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <i class="fas fa-desktop"></i>
                </span>
                <span>Bàn điều khiển</span>
            </a>

            <a href="{{ route('receptionist.walkins') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.walkins') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span>Register Walk-in</span>
            </a>

            <a href="{{ route('receptionist.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.appointments') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <i class="fas fa-calendar-check"></i>
                </span>
                <span>Today's Appointments</span>
            </a>

            <a href="{{ route('receptionist.billing') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.billing') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
                <span>Thanh toán viện phí</span>
            </a>
        </div>

        <div id="nav-vet" class="hidden">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Vet</div>

            <a href="{{ route('vet.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.appointments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <path d="M3 10h18"></path>
                        <path d="M8 14h8"></path>
                    </svg>
                </span>
                <span>Lich kham cua toi</span>
            </a>
        </div>

        <div id="nav-admin" class="hidden">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Admin</div>

            <a href="{{ route('admin.medicines') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.medicines') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M9 12h6"></path>
                        <path d="M10 16h4"></path>
                        <path d="M8 3h8l4 4v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                        <path d="M14 3v4h4"></path>
                    </svg>
                </span>
                <span>Quan ly thuoc</span>
            </a>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('petcare_sanctum_token');
        if (token) {
            try {
                const res = await fetch('/api/user', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const user = await res.json();
                if (user && user.role) {
                    if (user.role === 'owner') {
                        document.getElementById('nav-owner')?.classList.remove('hidden');
                    } else if (user.role === 'vet') {
                        document.getElementById('nav-vet')?.classList.remove('hidden');
                    } else if (user.role === 'receptionist') {
                        document.getElementById('nav-receptionist')?.classList.remove('hidden');
                    } else if (user.role === 'admin') {
                        document.getElementById('nav-admin')?.classList.remove('hidden');
                    }
                }
            } catch (e) {
                console.error('Failed to load user role for sidebar');
            }
        }
    });
</script>
