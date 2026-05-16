<x-layout.app
    title="Lịch hẹn của chủ nuôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-appointments"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Thêm thú cưng</h2>
            <p class="mt-2 text-sm text-[#4A4A4A]">Trước khi tạo lịch hẹn, hãy đảm bảo hồ sơ thú cưng của bạn đã có sẵn.</p>

            <div class="mt-4 rounded-2xl border border-dashed border-[#C7CDD5] bg-[#F8FAFC] p-4">
                <p class="text-sm text-[#4A4A4A]">Mở phần quản lý thú cưng để thêm hoặc cập nhật thông tin trước.</p>
                <a
                    href="{{ route('owner.pets') }}"
                    class="mt-3 inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]"
                >
                    Đi tới phần thêm thú cưng
                </a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Tạo lịch hẹn</h2>
            <p id="owner-appointments-status" class="mt-2 text-sm text-[#4A4A4A]">Đang tải thú cưng...</p>

            <form id="owner-appointment-form" class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Thú cưng</label>
                    <select id="appointment-pet-select" name="pet_id" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                        <option>Chọn thú cưng từ danh sách của bạn</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Ngày hẹn</label>
                    <input id="appointment-date" name="appointment_date" type="date" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Giờ hẹn</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="appointment-hour" name="appointment_hour" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="08">08</option>
                            <option value="09" selected>09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                        <select id="appointment-minute" name="appointment_minute" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="00" selected>00</option>
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="45">45</option>
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Lý do</label>
                    <textarea id="appointment-reason" name="reason" rows="4" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Mô tả triệu chứng của thú cưng..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">
                        Tạo lịch hẹn
                    </button>
                </div>
            </form>

            <div class="mt-6 rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Lịch hẹn sắp tới</h3>
                <div id="owner-appointments-list" class="mt-2 space-y-2 text-sm text-[#4A4A4A]"></div>
            </div>
        </article>
    </section>
</x-layout.app>
