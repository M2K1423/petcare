<x-layout.app
    title="Quên mật khẩu | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="auth-sanctum"
    :showHeader="false"
    :showFooter="false"
>
    <div class="flex min-h-[calc(100vh-5rem)] items-center justify-center">
    <section class="w-full max-w-md rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_18px_44px_rgba(0,0,0,0.05)] backdrop-blur md:p-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Khôi phục mật khẩu</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#333333]">Quên mật khẩu</h1>
            </div>
            <span class="rounded-full border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Bảo mật</span>
        </div>

        <p class="text-sm text-[#4A4A4A]">Nhập email đã đăng ký. Chúng tôi sẽ gửi mã OTP khôi phục mật khẩu cho bạn.</p>
        <p id="sanctum-status" class="mt-4 text-sm text-[#4A4A4A]">Sẵn sàng.</p>

        <form id="sanctum-forgot-form" class="mt-5 space-y-4">
            <div>
                <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email tài khoản</label>
                <input id="email" name="email" type="email" required placeholder="your-email@example.com" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">Gửi mã xác thực</button>
        </form>

        <p class="mt-6 text-sm text-[#4A4A4A]">
            Nhớ mật khẩu?
            <a href="{{ route('sanctum.auth') }}" class="font-semibold text-[#2A6496] transition hover:underline">Đăng nhập</a>
        </p>
    </section>
    </div>
</x-layout.app>
