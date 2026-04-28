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

            <a href="{{ route('vet.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M3 13h8V3H3z"></path>
                        <path d="M13 21h8v-8h-8z"></path>
                        <path d="M13 3h8v6h-8z"></path>
                        <path d="M3 21h8v-4H3z"></path>
                    </svg>
                </span>
                <span>Dashboard bac si</span>
            </a>

            <a href="{{ route('vet.workflow.schedule') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.schedule') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">1</span>
                <span>Quan ly lich kham</span>
            </a>

            <a href="{{ route('vet.workflow.intake') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.intake') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">2</span>
                <span>Kham benh</span>
            </a>

            <a href="{{ route('vet.workflow.diagnosis') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.diagnosis') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">3</span>
                <span>Chan doan</span>
            </a>

            <a href="{{ route('vet.workflow.records') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.records') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">4</span>
                <span>Lap benh an</span>
            </a>

            <a href="{{ route('vet.workflow.orders') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.orders') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">5</span>
                <span>Chi dinh dich vu</span>
            </a>

            <a href="{{ route('vet.workflow.prescriptions') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.prescriptions') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">6</span>
                <span>Ke don thuoc</span>
            </a>

            <a href="{{ route('vet.workflow.procedures') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.procedures') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">7</span>
                <span>Dieu tri thu thuat</span>
            </a>

            <a href="{{ route('vet.workflow.vaccinations') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.vaccinations') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">8</span>
                <span>Quan ly tiem phong</span>
            </a>

            <a href="{{ route('vet.workflow.inpatient') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.inpatient') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">9</span>
                <span>Theo doi noi tru</span>
            </a>

            <a href="{{ route('vet.workflow.follow-up') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.follow-up') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">10</span>
                <span>Tai kham</span>
            </a>

            <a href="{{ route('vet.workflow.sign-off') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.sign-off') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">11</span>
                <span>Ky xac nhan</span>
            </a>

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

            <a href="{{ route('vet.schedule-week') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.schedule-week') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">12</span>
                <span>Lich theo tuan</span>
            </a>
        </div>

        <div id="nav-admin" class="hidden">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Admin</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">📊</span>
                <span>Dashboard</span>
            </a>

            <div class="mt-2 border-t border-[#163045] pt-2"></div>

            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.users*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">👥</span>
                <span>Quản lý nhân sự</span>
            </a>

            <a href="{{ route('admin.doctors') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.doctors*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">🏥</span>
                <span>Bác sĩ</span>
            </a>

            <a href="{{ route('admin.services') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.services*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">⚕️</span>
                <span>Dịch vụ</span>
            </a>

            <a href="{{ route('admin.pets') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.pets*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">🐾</span>
                <span>Thú cưng</span>
            </a>

            <a href="{{ route('admin.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.appointments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">📅</span>
                <span>Lịch Hẹn</span>
            </a>

            <div class="mt-3 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Kho & Tài chính</div>

            <a href="{{ route('admin.medicines') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.medicines*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">💊</span>
                <span>Thuốc</span>
            </a>

            <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.inventory*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">📦</span>
                <span>Quản lý kho</span>
            </a>

            <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.payments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">💰</span>
                <span>Doanh thu</span>
            </a>

            <div class="mt-3 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Báo cáo & Hệ thống</div>

            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.reports*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">📈</span>
                <span>Báo cáo</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.settings*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">⚙️</span>
                <span>Thiết lập hệ thống</span>
            </a>

            <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.logs*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">📋</span>
                <span>Nhật ký vận hành</span>
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
