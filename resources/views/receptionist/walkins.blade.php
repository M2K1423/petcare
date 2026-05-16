<x-layout.app
    title="Đăng ký khách vãng lai | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="receptionist-walkins"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Đăng ký khách vãng lai</h1>
            <p class="text-sm text-gray-500">Tạo hồ sơ khách vãng lai mới và đưa thú cưng vào hàng chờ.</p>
        </div>
        <a href="{{ route('receptionist.appointments') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Xem lịch hẹn hôm nay
        </a>
    </div>

    <article class="w-full rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form id="walkin-form" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Thông tin chủ nuôi</label>
                <input type="text" name="owner_name" placeholder="Tên khách hàng" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="tel" name="owner_phone" placeholder="Số điện thoại" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="email" name="owner_email" placeholder="Địa chỉ email (không bắt buộc)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Thông tin thú cưng</label>
                <input type="text" name="pet_name" placeholder="Tên thú cưng" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="number" step="0.1" name="pet_weight" placeholder="Cân nặng (kg) (không bắt buộc)" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                <select name="species_id" id="species-select" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                    <option value="">Đang tải loài...</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phân công bác sĩ (không bắt buộc)</label>
                <select name="doctor_id" id="doctor-select" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Tự động phân công sau</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tình trạng</label>
                <select name="condition_option" id="condition-option" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Chọn tình trạng</option>
                    <option value="General check-up">Khám tổng quát</option>
                    <option value="Vaccination">Tiêm phòng</option>
                    <option value="Digestive issue">Vấn đề tiêu hóa</option>
                    <option value="Skin issue">Vấn đề da liễu</option>
                    <option value="Injury">Chấn thương</option>
                    <option value="Other">Khác (tự nhập bên dưới)</option>
                </select>
                <input type="text" name="condition_custom" id="condition-custom" placeholder="Hoặc nhập chi tiết tình trạng" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_emergency" name="is_emergency" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_emergency" class="text-sm font-medium text-red-600">Đánh dấu khẩn cấp</label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-500">
                Đăng ký và thêm vào hàng chờ
            </button>
        </form>

        <div id="walkin-message" class="mt-3 hidden text-sm font-medium"></div>
    </article>
</x-layout.app>
