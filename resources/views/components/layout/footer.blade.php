<footer id="site-footer" class="pc-footer-night relative z-10 mt-auto overflow-hidden border-t border-white/20 bg-[#2A6496] text-white">
    <span class="pc-footer-dot pc-footer-dot-a" aria-hidden="true"></span>
    <span class="pc-footer-dot pc-footer-dot-b" aria-hidden="true"></span>
    <span class="pc-footer-dot pc-footer-dot-c" aria-hidden="true"></span>
    <span class="pc-footer-dot pc-footer-dot-d" aria-hidden="true"></span>
    <span class="pc-footer-dot pc-footer-dot-e" aria-hidden="true"></span>

    <div class="relative mx-auto w-full max-w-6xl px-4 py-10 md:px-8">
        <div class="grid gap-6 text-sm md:grid-cols-3">
            <section>
                <p class="text-xs uppercase tracking-[0.2em] text-white">PetCare</p>
                <h3 class="mt-2 text-lg font-semibold text-white">Trung tâm quản lý phòng khám</h3>
                <p class="mt-2 text-[#D1E0F3]">Quản lý thú cưng, lịch hẹn và hồ sơ chăm sóc trong một cổng chủ nuôi an toàn.</p>
            </section>

            <section>
                <p class="text-xs uppercase tracking-[0.2em] text-white">Liên kết nhanh</p>
                <ul class="mt-3 space-y-2 text-white">
                    <li><a href="{{ route('sanctum.auth') }}" class="transition hover:text-[#D1E0F3]">Đăng nhập</a></li>
                    <li><a href="{{ route('sanctum.auth.register') }}" class="transition hover:text-[#D1E0F3]">Tạo tài khoản chủ nuôi</a></li>
                    <li><a href="{{ route('owner.pets') }}" class="transition hover:text-[#D1E0F3]">Bảng thú cưng của chủ nuôi</a></li>
                </ul>
            </section>

            <section>
                <p class="text-xs uppercase tracking-[0.2em] text-white">Trạng thái hệ thống</p>
                <div class="mt-3 space-y-2 text-[#D1E0F3]">
                    <p><span class="text-white">Chế độ:</span> Phát triển cục bộ</p>
                    <p><span class="text-white">Bảo mật:</span> Phân quyền theo vai trò</p>
                    <p><span class="text-white">Xác thực API:</span> Laravel Sanctum</p>
                </div>
            </section>
        </div>

        <div class="mt-6 flex flex-col gap-2 border-t border-white/20 pt-4 text-xs md:flex-row md:items-center md:justify-between">
            <p class="text-[#D1E0F3]">Quản lý phòng khám PetCare</p>
            <p class="uppercase tracking-[0.16em] text-[#D1E0F3]">Cổng chủ nuôi an toàn</p>
        </div>
    </div>
</footer>
