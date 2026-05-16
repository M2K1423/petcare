<x-layout.app
    title="Thú cưng của chủ nuôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-pets"
    :showSidebar="true"
>
    <div class="flex flex-col gap-5">
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Thêm thú cưng</h2>

            <form id="owner-pet-form" class="mt-4 space-y-3">
                <div>
                        <label for="pet-name" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Tên</label>
                    <input id="pet-name" name="name" required type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>

                <div>
                    <label for="pet-species" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Loài</label>
                    <select id="pet-species" name="species_id" required class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></select>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="pet-gender" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giới tính</label>
                        <select id="pet-gender" name="gender" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20">
                            <option value="unknown" selected>Không rõ</option>
                            <option value="male">Đực</option>
                            <option value="female">Cái</option>
                        </select>
                    </div>
                    <div>
                        <label for="pet-weight" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Cân nặng (kg)</label>
                        <input id="pet-weight" name="weight" type="number" min="0" step="0.01" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="pet-breed" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giống</label>
                        <input id="pet-breed" name="breed" type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                    </div>
                    <div>
                        <label for="pet-birth-date" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ngày sinh</label>
                        <input id="pet-birth-date" name="birth_date" type="date" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                    </div>
                </div>

                <div>
                    <label for="pet-color" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Màu sắc</label>
                    <input id="pet-color" name="color" type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20" />
                </div>

                <div>
                    <label for="pet-allergies" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Dị ứng</label>
                    <textarea id="pet-allergies" name="allergies" rows="2" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
                </div>

                <div>
                    <label for="pet-notes" class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ghi chú</label>
                    <textarea id="pet-notes" name="notes" rows="2" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">
                    Tạo thú cưng
                </button>
            </form>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Thú cưng của bạn</h2>
            <div id="owner-pets-list" class="mt-4 space-y-3"></div>
        </article>
    </section>
    </div>
</x-layout.app>
