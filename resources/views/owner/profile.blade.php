<x-layout.app
    title="Owner Profile | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-profile"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-2">
            <h1 class="text-2xl font-bold text-[#333333]">Profile</h1>
            <p class="mt-2 text-sm text-[#4A4A4A]">Update your account information used for appointment booking and contact.</p>

            <p id="owner-profile-status" class="mt-4 text-sm text-[#4A4A4A]">Loading your profile...</p>

            <form id="owner-profile-form" class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Full name</label>
                    <input id="owner-profile-name" name="name" type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Your full name" />
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email</label>
                    <input id="owner-profile-email" name="email" type="email" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="you@example.com" />
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">
                        Save profile
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Account info</h2>
            <dl class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
                <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2">
                    <dt class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">Role</dt>
                    <dd id="owner-profile-role" class="mt-1 font-semibold text-[#333333]">-</dd>
                </div>
                <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2">
                    <dt class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">User ID</dt>
                    <dd id="owner-profile-id" class="mt-1 font-semibold text-[#333333]">-</dd>
                </div>
            </dl>

            <div class="mt-5 rounded-xl border border-dashed border-[#C7CDD5] bg-[#F8FAFC] p-3 text-sm text-[#4A4A4A]">
                Need to manage pets or appointments?
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('owner.pets') }}" class="rounded-lg border border-[#C1C4C9] bg-[#FFFFFF] px-3 py-1.5 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Pets</a>
                    <a href="{{ route('owner.appointments') }}" class="rounded-lg border border-[#C1C4C9] bg-[#FFFFFF] px-3 py-1.5 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Appointments</a>
                </div>
            </div>
        </article>
    </section>
</x-layout.app>
