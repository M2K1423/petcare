<x-layout.app
    title="Register | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/pages/sanctum-auth.ts']"
    :showHeader="false"
    :showFooter="false"
>
    <div class="flex min-h-[calc(100vh-5rem)] items-center justify-center">
    <section class="w-full max-w-md rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_18px_44px_rgba(0,0,0,0.05)] backdrop-blur md:p-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Owner Onboarding</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-[#333333]">Create Account</h1>
            </div>
            <span class="rounded-full border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-[#4A4A4A]">New</span>
        </div>

        <p class="text-sm text-[#4A4A4A]">Register a new owner account to start using the system.</p>
        <p id="sanctum-status" class="mt-4 text-sm text-[#4A4A4A]">Ready.</p>

        <form id="sanctum-register-form" class="mt-5 space-y-4">
            <div>
                <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Full name</label>
                <input id="name" name="name" type="text" required placeholder="Pet owner name" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email</label>
                <input id="email" name="email" type="email" required placeholder="owner@example.com" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <div>
                <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Password</label>
                <input id="password" name="password" type="password" minlength="6" required placeholder="At least 6 characters" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2.5 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">Register</button>
        </form>

        <p class="mt-6 text-sm text-[#4A4A4A]">
            Already have an account?
            <a href="{{ route('sanctum.auth') }}" class="font-semibold text-[#2A6496] transition hover:underline">Back to login</a>
        </p>
    </section>
    </div>
</x-layout.app>
