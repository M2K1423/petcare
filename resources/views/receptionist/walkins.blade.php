<x-layout.app
    title="Register Walk-in | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/pages/receptionist-appointments.ts']"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Register Walk-in</h1>
            <p class="text-sm text-gray-500">Create a new walk-in profile and push pet to queue.</p>
        </div>
        <a href="{{ route('receptionist.appointments') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            View Today's Appointments
        </a>
    </div>

    <article class="w-full rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <form id="walkin-form" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Owner Information</label>
                <input type="text" name="owner_name" placeholder="Customer Name" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="tel" name="owner_phone" placeholder="Phone Number" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="email" name="owner_email" placeholder="Email Address (Optional)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Pet Information</label>
                <input type="text" name="pet_name" placeholder="Pet Name" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                <input type="number" step="0.1" name="pet_weight" placeholder="Weight (kg) (Optional)" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                <select name="species_id" id="species-select" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500" required>
                    <option value="">Loading species...</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Assign Doctor (Optional)</label>
                <select name="doctor_id" id="doctor-select" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Auto assign later</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Condition</label>
                <select name="condition_option" id="condition-option" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Select condition</option>
                    <option value="General check-up">General check-up</option>
                    <option value="Vaccination">Vaccination</option>
                    <option value="Digestive issue">Digestive issue</option>
                    <option value="Skin issue">Skin issue</option>
                    <option value="Injury">Injury</option>
                    <option value="Other">Other (type manually below)</option>
                </select>
                <input type="text" name="condition_custom" id="condition-custom" placeholder="Or type condition details" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_emergency" name="is_emergency" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_emergency" class="text-sm font-medium text-red-600">Mark as Emergency</label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-500">
                Register & Add to Queue
            </button>
        </form>

        <div id="walkin-message" class="mt-3 hidden text-sm font-medium"></div>
    </article>
</x-layout.app>
