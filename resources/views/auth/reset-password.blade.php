<x-layout.app
    title="Đặt lại mật khẩu | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="auth-sanctum"
    :showHeader="false"
    :showFooter="false"
>
    <div class="flex min-h-[calc(100vh-5rem)] items-center justify-center">
    <section class="w-full max-w-md rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_18px_44px_rgba(0,0,0,0.05)] backdrop-blur md:p-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Đặt lại mật khẩu</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#333333]">Đổi mật khẩu</h1>
            </div>
            <span class="rounded-full border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Xác thực</span>
        </div>

        <p class="text-sm text-[#4A4A4A]">Nhập mã OTP 6 số được gửi và mật khẩu mới để khôi phục tài khoản.</p>
        <p id="sanctum-status" class="mt-4 text-sm text-[#4A4A4A]">Sẵn sàng.</p>

        <form id="sanctum-reset-form" class="mt-5 space-y-4">
            <div>
                <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email tài khoản</label>
                <input id="email" name="email" type="email" required placeholder="your-email@example.com" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="otp" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Mã OTP 6 số</label>
                <input id="otp" name="otp" type="text" required maxlength="6" placeholder="xxxxxx" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20 text-center tracking-[0.5em] font-bold" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Mật khẩu mới</label>
                <input id="password" name="password" type="password" required placeholder="Tối thiểu 6 ký tự" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Xác nhận mật khẩu</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Nhập lại mật khẩu mới" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">Đặt lại mật khẩu</button>
        </form>

        <p class="mt-6 text-sm text-[#4A4A4A]">
            Quay lại?
            <a href="{{ route('sanctum.auth') }}" class="font-semibold text-[#2A6496] transition hover:underline">Đăng nhập</a>
        </p>
    </section>
    </div>
</x-layout.app>
