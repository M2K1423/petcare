<x-layout.app
    title="Sanctum Auth | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/pages/sanctum-auth.ts']"
    :showHeader="false"
    :showFooter="false"
>
    <div class="flex min-h-[calc(100vh-5rem)] items-center justify-center">
    <section class="w-full max-w-md rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_18px_44px_rgba(0,0,0,0.05)] backdrop-blur md:p-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Owner Access</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#333333]">Sign In</h1>
            </div>
            <span class="rounded-full border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">Secure</span>
        </div>

        <p class="text-sm text-[#4A4A4A]">Login to continue to your clinic workspace.</p>
        <p id="sanctum-status" class="mt-4 text-sm text-[#4A4A4A]">Ready.</p>

        <form id="sanctum-login-form" class="mt-5 space-y-4">
            <div>
                <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email</label>
                <input id="email" name="email" type="email" required placeholder="staff@petcare.vn" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Password</label>
                <input id="password" name="password" type="password" required placeholder="Enter your password" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">Sign in</button>
        </form>

        <p class="mt-6 text-sm text-[#4A4A4A]">
            New here?
            <a href="{{ route('sanctum.auth.register') }}" class="font-semibold text-[#2A6496] transition hover:underline">Create account</a>
        </p>
    </section>
    </div>
</x-layout.app>
