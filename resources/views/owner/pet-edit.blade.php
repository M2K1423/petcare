<x-layout.app
    title="Chỉnh sửa thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-pet-edit"
    :showSidebar="true"
>
    <header class="mb-6 rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[#4A4A4A]">Không gian chủ nuôi</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[#333333] md:text-3xl">Chỉnh sửa thú cưng</h1>
                <p class="mt-1 text-sm text-[#4A4A4A]">Cập nhật thông tin chi tiết cho thú cưng của bạn.</p>
            </div>
            <a href="{{ route('owner.pets') }}" class="rounded-lg border border-[#C1C4C9] bg-[#F1F3F5] px-3 py-1.5 text-xs font-semibold text-[#4A4A4A] transition hover:border-[#2A6496] hover:text-[#2A6496]">Quay lại danh sách</a>
        </div>
        <p id="owner-pet-edit-status" class="mt-3 text-sm text-[#4A4A4A]">Đang tải...</p>
    </header>

    <section id="owner-pet-edit-root" data-pet-id="{{ $petId }}" class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur">
        <form id="owner-pet-edit-form" class="space-y-3">
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
                        <option value="unknown">Không rõ</option>
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
                <textarea id="pet-notes" name="notes" rows="3" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none transition focus:border-[#2A6496] focus:ring-2 focus:ring-[#2A6496]/20"></textarea>
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] focus:outline-none focus:ring-2 focus:ring-[#2A6496]/35">
                Lưu thay đổi
            </button>
        </form>
    </section>
</x-layout.app>