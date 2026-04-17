<x-layout.app
    title="Today's Appointments | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/pages/receptionist-appointments.ts']"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Today's Appointments</h1>
            <p class="text-sm text-gray-500">Review and confirm appointment check-ins for the day.</p>
        </div>
        <a href="{{ route('receptionist.walkins') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            New Walk-in
        </a>
    </div>

    <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-gray-900">Appointments</h2>
            <div class="flex flex-wrap items-center gap-2">
                <input id="appointments-date" type="date" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-500">
                <button id="btn-filter-appointments" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">View</button>
                <button id="btn-today-appointments" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Today</button>
                <button id="btn-refresh-appointments" class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-sync-alt"></i> Refresh</button>
            </div>
        </div>

        <div id="appointments-container" class="flex flex-col gap-3">
            <div class="rounded-xl bg-gray-50 p-4 text-center text-sm text-gray-500">Loading...</div>
        </div>
    </article>

</x-layout.app>