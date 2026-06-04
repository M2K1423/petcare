<!-- Mobile Sidebar Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 z-[55] bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"></div>

<aside id="main-sidebar" class="fixed inset-y-0 left-0 z-[60] flex h-screen w-80 -translate-x-full flex-col border-r border-[#1E2C43] bg-gradient-to-b from-[#0F1E35] to-[#142845] text-[#C8D4E8] shadow-[0_20px_40px_rgba(0,0,0,0.28)] transition-transform duration-300 ease-in-out lg:translate-x-0">
    <div class="flex-1 overflow-y-auto px-4 py-6">
    <div class="flex items-center justify-between border border-[#2A3D5E] bg-[#122742]/70 px-3 py-2">
        <div>
            <p class="text-[11px] uppercase tracking-[0.18em] text-[#7FA6D9]">Quản lý</p>
            <p class="mt-1 text-sm font-semibold text-[#EAF2FF]">Bảng điều khiển PetCare</p>
        </div>
        <!-- Close button for mobile -->
        <button id="mobile-sidebar-close" class="rounded-lg p-1.5 text-[#88A8D8] hover:bg-[#1E3657] hover:text-white transition lg:hidden" aria-label="Đóng sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <nav class="mt-4 space-y-1 text-sm" id="sidebar-nav-container">
        
        <!-- DÀNH CHO CHỦ THÚ CƯNG -->
        <div id="nav-owner" class="{{ request()->routeIs('owner.*') ? '' : 'hidden' }}">
            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Dành cho chủ thú cưng</div>
            <a href="{{ route('owner.overview') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.overview') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="4" x2="20" y1="12" y2="12"></line>
                        <line x1="4" x2="20" y1="6" y2="6"></line>
                        <line x1="4" x2="20" y1="18" y2="18"></line>
                    </svg>
                </span>
                <span>Tổng quan</span>
            </a>

            <a href="{{ route('owner.profile') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.profile') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span>Hồ sơ</span>
            </a>

            <a href="{{ route('owner.pets') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.pets*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="11" cy="4" r="2"></circle>
                        <circle cx="18" cy="8" r="2"></circle>
                        <circle cx="20" cy="12" r="2"></circle>
                        <path d="M12 10a4 4 0 0 0-4 4c0 3 3 3 3 6h2c0-3 3-3 3-6a4 4 0 0 0-4-4z"></path>
                    </svg>
                </span>
                <span>Quản lý thú cưng</span>
            </a>

            <a href="{{ route('owner.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.appointments') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M16 2v4"></path>
                        <path d="M8 2v4"></path>
                        <path d="M3 10h18"></path>
                    </svg>
                </span>
                <span>Lịch hẹn khám</span>
            </a>

            <a href="{{ route('owner.shop') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.shop') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                </span>
                <span>Mua thuốc</span>
            </a>

            <a href="{{ route('owner.orders') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.orders') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <path d="M9 14h6"></path>
                        <path d="M9 18h6"></path>
                        <path d="M9 10h6"></path>
                    </svg>
                </span>
                <span>Lịch Sử Đơn Hàng</span>
            </a>

            <a href="{{ route('owner.ai-diagnosis') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('owner.ai-diagnosis') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </span>
                <span>Chẩn đoán AI</span>
            </a>


        </div>
        
        <!-- DÀNH CHO LỄ TÂN -->
        <div id="nav-receptionist" class="{{ request()->routeIs('receptionist.*') ? '' : 'hidden' }}">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Dành cho lễ tân</div>
            
            <a href="{{ route('receptionist.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="10" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                </span>
                <span>Bàn điều khiển</span>
            </a>

            <a href="{{ route('receptionist.walkins') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.walkins') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" x2="19" y1="8" y2="14"></line>
                        <line x1="22" x2="16" y1="11" y2="11"></line>
                    </svg>
                </span>
                <span>Đăng ký khách vãng lai</span>
            </a>

            <a href="{{ route('receptionist.shop') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.shop') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                </span>
                <span>Cửa hàng (Bán thuốc)</span>
            </a>

            <a href="{{ route('receptionist.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.appointments') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M16 2v4"></path>
                        <path d="M8 2v4"></path>
                        <path d="M3 10h18"></path>
                        <path d="m9 16 2 2 4-4"></path>
                    </svg>
                </span>
                <span>Lịch hẹn hôm nay</span>
            </a>

            <a href="{{ route('receptionist.billing') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('receptionist.billing') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18l-3-1-3 1-3-1-3 1-3-1-3 1z"></path>
                        <path d="M16 8H8"></path>
                        <path d="M16 12H8"></path>
                        <path d="M12 16H8"></path>
                    </svg>
                </span>
                <span>Thanh toán viện phí</span>
            </a>
        </div>

        <div id="nav-vet" class="{{ request()->routeIs('vet.*') ? '' : 'hidden' }}">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Bác sĩ</div>

            <a href="{{ route('vet.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M3 13h8V3H3z"></path>
                        <path d="M13 21h8v-8h-8z"></path>
                        <path d="M13 3h8v6h-8z"></path>
                        <path d="M3 21h8v-4H3z"></path>
                    </svg>
                </span>
                <span>Bảng điều khiển bác sĩ</span>
            </a>

            {{--
            <a href="{{ route('vet.workflow.schedule') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.schedule') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </span>
                <span>Quản lý lịch khám</span>
            </a>

            <a href="{{ route('vet.workflow.intake') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M6 18h12"></path>
                        <path d="M12 2v16"></path>
                        <path d="M8 8a4 4 0 0 1 8 0v2a4 4 0 0 1-8 0z"></path>
                    </svg>
                </span>
                <span>Khám bệnh</span>
            </a>

            <a href="{{ route('vet.workflow.diagnosis') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.diagnosis') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M2 12h4"></path>
                        <path d="M18 12h4"></path>
                        <path d="M12 2v4"></path>
                        <path d="M12 18v4"></path>
                    </svg>
                </span>
                <span>Chẩn đoán</span>
            </a>

            <a href="{{ route('vet.workflow.records') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.records') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M7 3h10l4 4v14H7z"></path>
                        <path d="M14 3v5h5"></path>
                        <path d="M9 13h6"></path>
                        <path d="M9 17h6"></path>
                    </svg>
                </span>
                <span>Lập bệnh án</span>
            </a>

            <a href="{{ route('vet.workflow.orders') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.orders') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M4 6h16"></path>
                        <path d="M7 6v12"></path>
                        <path d="M17 6v12"></path>
                        <path d="M7 12h10"></path>
                    </svg>
                </span>
                <span>Chỉ định dịch vụ</span>
            </a>

            <a href="{{ route('vet.workflow.prescriptions') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.prescriptions') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M10 2v6"></path>
                        <path d="M14 2v6"></path>
                        <path d="M8 8h8"></path>
                        <path d="M7 8l-1 14h12l-1-14"></path>
                    </svg>
                </span>
                <span>Kê đơn thuốc</span>
            </a>

            <a href="{{ route('vet.workflow.procedures') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.procedures') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M4 20l6-6"></path>
                        <path d="M14 4l6 6"></path>
                        <path d="M7 17l10-10"></path>
                        <path d="M14 14l3 3"></path>
                    </svg>
                </span>
                <span>Điều trị thủ thuật</span>
            </a>

            <a href="{{ route('vet.workflow.vaccinations') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.vaccinations') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M3 7h18"></path>
                        <path d="M7 7v14h10V7"></path>
                        <path d="M9 11h6"></path>
                        <path d="M9 15h6"></path>
                    </svg>
                </span>
                <span>Quản lý tiêm phòng</span>
            </a>

            <a href="{{ route('vet.workflow.inpatient') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.inpatient') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M3 12h18"></path>
                        <path d="M5 12V8a2 2 0 0 1 2-2h4"></path>
                        <path d="M19 12v6"></path>
                        <path d="M5 18h14"></path>
                    </svg>
                </span>
                <span>Theo dõi nội trú</span>
            </a>

            <a href="{{ route('vet.workflow.follow-up') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.follow-up') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M21 12a9 9 0 1 1-3-6.7"></path>
                        <path d="M21 3v6h-6"></path>
                    </svg>
                </span>
                <span>Tái khám</span>
            </a>

            <a href="{{ route('vet.workflow.sign-off') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.workflow.sign-off') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path d="M20 6 9 17l-5-5"></path>
                        <path d="M4 20h16"></path>
                    </svg>
                </span>
                <span>Ký xác nhận</span>
            </a>

            --}}

            <a href="{{ route('vet.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.appointments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M16 2v4"></path>
                        <path d="M8 2v4"></path>
                        <path d="M3 10h18"></path>
                        <path d="M8 14h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M16 14h.01"></path>
                        <path d="M8 18h.01"></path>
                        <path d="M12 18h.01"></path>
                        <path d="M16 18h.01"></path>
                    </svg>
                </span>
                <span>Lịch khám của tôi</span>
            </a>

            <a href="{{ route('vet.schedule-week') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.schedule-week') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M16 2v4"></path>
                        <path d="M8 2v4"></path>
                        <path d="M3 10h18"></path>
                        <path d="M8 14h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M16 14h.01"></path>
                    </svg>
                </span>
                <span>Lịch theo tuần</span>
            </a>

            <a href="{{ route('vet.ai-diagnosis') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('vet.ai-diagnosis') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </span>
                <span>Chẩn đoán AI</span>
            </a>
        </div>

        <div id="nav-admin" class="{{ request()->routeIs('admin.*') ? '' : 'hidden' }}">
            <div class="mt-4 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Quản trị</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="10" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                </span>
                <span>Bảng điều khiển</span>
            </a>

            <div class="mt-2 border-t border-[#163045] pt-2"></div>

            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.users*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span>Quản lý nhân sự</span>
            </a>

            <a href="{{ route('admin.doctors') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.doctors*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M4.8 2.8A2.4 2.4 0 0 0 2.4 5.2v2.4a8.4 8.4 0 0 0 16.8 0V5.2a2.4 2.4 0 0 0-2.4-2.4Z"></path>
                        <path d="M12 16v6"></path>
                        <path d="M12 22a3 3 0 0 0 3-3"></path>
                    </svg>
                </span>
                <span>Bác sĩ</span>
            </a>

            <a href="{{ route('admin.services') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.services*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </span>
                <span>Dịch vụ</span>
            </a>

            <a href="{{ route('admin.pets') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.pets*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="11" cy="4" r="2"></circle>
                        <circle cx="18" cy="8" r="2"></circle>
                        <circle cx="20" cy="12" r="2"></circle>
                        <path d="M12 10a4 4 0 0 0-4 4c0 3 3 3 3 6h2c0-3 3-3 3-6a4 4 0 0 0-4-4z"></path>
                    </svg>
                </span>
                <span>Thú cưng</span>
            </a>

            <a href="{{ route('admin.appointments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.appointments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M16 2v4"></path>
                        <path d="M8 2v4"></path>
                        <path d="M3 10h18"></path>
                    </svg>
                </span>
                <span>Lịch Hẹn</span>
            </a>

            <div class="mt-3 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Kho & tài chính</div>

            <a href="{{ route('admin.medicines') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.medicines*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"></path>
                        <path d="m8.5 8.5 7 7"></path>
                    </svg>
                </span>
                <span>Thuốc</span>
            </a>

            <a href="{{ route('admin.inventory') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.inventory*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </span>
                <span>Quản lý kho</span>
            </a>

            <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.payments*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                        <path d="M12 6v12"></path>
                    </svg>
                </span>
                <span>Doanh thu</span>
            </a>

            <div class="mt-3 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Báo cáo & hệ thống</div>

            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.reports*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                </span>
                <span>Báo cáo</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.settings*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
                <span>Thiết lập hệ thống</span>
            </a>

            <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-3 py-2 transition {{ request()->routeIs('admin.logs*') ? 'bg-[#1E3657] text-[#F5FAFF]' : 'text-[#B8C7DE] hover:bg-[#1A304E] hover:text-[#F5FAFF]' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center text-[#88A8D8]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <path d="M9 14h6"></path>
                        <path d="M9 18h6"></path>
                        <path d="M9 10h6"></path>
                    </svg>
                </span>
                <span>Nhật ký vận hành</span>
            </a>
        </div>
    </nav>
    </div>

    <!-- User Profile Footer pinned at bottom -->
    <div class="mt-auto border-t border-[#1E2C43] bg-[#0A1629] p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#1E3657] font-bold text-white shadow-inner" id="sidebar-user-avatar">
                U
            </div>
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-white truncate max-w-[150px]" id="sidebar-user-name">Đang tải...</p>
                <p class="text-xs text-[#88A8D8] uppercase tracking-wide mt-0.5" id="sidebar-user-role">Chức vụ</p>
            </div>
        </div>
        <button onclick="handleLogout()" class="rounded-full p-2 text-[#88A8D8] hover:bg-[#1E3657] hover:text-white transition" title="Đăng xuất">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" x2="9" y1="12" y2="12"></line>
            </svg>
        </button>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Mobile sidebar toggle logic
        const toggleBtn = document.getElementById('mobile-sidebar-toggle');
        const closeBtn = document.getElementById('mobile-sidebar-close');
        const sidebar = document.getElementById('main-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            if (sidebar) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
            }
            if (backdrop) {
                backdrop.classList.remove('hidden');
            }
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            if (sidebar) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
            document.body.classList.remove('overflow-hidden');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        const token = localStorage.getItem('petcare_sanctum_token');
        if (token) {
            try {
                const res = await fetch('/api/auth/me', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                const user = data.user;
                if (user) {
                    // Update user info
                    document.getElementById('sidebar-user-name').innerText = user.name || 'Người dùng';
                    document.getElementById('sidebar-user-avatar').innerText = (user.name || 'U').charAt(0).toUpperCase();
                    
                    if (user.role && typeof user.role === 'object') {
                        document.getElementById('sidebar-user-role').innerText = user.role.name || user.role.slug;
                    } else if (user.role) {
                        document.getElementById('sidebar-user-role').innerText = user.role;
                    }

                    // Show menus
                    const roleSlug = typeof user.role === 'object' ? user.role.slug : user.role;
                    if (roleSlug === 'owner') {
                        document.getElementById('nav-owner')?.classList.remove('hidden');
                    } else if (roleSlug === 'vet') {
                        document.getElementById('nav-vet')?.classList.remove('hidden');
                    } else if (roleSlug === 'receptionist') {
                        document.getElementById('nav-receptionist')?.classList.remove('hidden');
                    } else if (roleSlug === 'admin') {
                        document.getElementById('nav-admin')?.classList.remove('hidden');
                    }
                }
            } catch (e) {
                console.error('Failed to load user role for sidebar', e);
            }
        }
    });

    function handleLogout() {
        if(confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            localStorage.removeItem('petcare_sanctum_token');
            window.location.href = '/login';
        }
    }
</script>
