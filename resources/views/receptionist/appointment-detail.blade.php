<x-layout.app
    title="Appointment Detail | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="receptionist-appointment-detail"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Appointment Detail</h1>
            <p class="text-sm text-gray-500">View full information of selected appointment.</p>
        </div>
        <a href="{{ route('receptionist.appointments') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            Back to Appointments
        </a>
    </div>

    <article id="appointment-detail-root" data-appointment-id="{{ $appointmentId }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div id="appointment-detail-content" class="space-y-3 text-sm text-gray-700">
            <div class="rounded-xl bg-gray-50 p-4 text-center text-gray-500">Loading appointment details...</div>
        </div>
    </article>
</x-layout.app>
