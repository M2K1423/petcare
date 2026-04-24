<x-layout.app
    title="Receptionist Dashboard | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="receptionist-dashboard"
    :showSidebar="true"
>
    <!-- Page Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Receptionist Dashboard</h1>
            <p class="text-sm text-gray-500">Manage queues, check-ins, and walk-ins.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="btn-sync-queue" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                <i class="fas fa-sync-alt mr-2"></i> Sync Queue
            </button>
            <a href="{{ route('receptionist.walkins') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                + New Walk-in
            </a>
            <a href="{{ route('receptionist.appointments') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Today's Appointments
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">Available Doctors</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900" id="stat-doctors">--</span>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">In Queue Today</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900" id="stat-queue">--</span>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-gray-500">Pending Payments</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-red-600" id="stat-unpaid">--</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Live Queue -->
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm col-span-2">
            <h2 class="text-lg font-bold text-gray-900">Live Waiting Queue</h2>
            <div id="queue-container" class="mt-4 flex flex-col gap-3">
                <div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Loading queue...</div>
            </div>
        </article>

        <!-- Available Doctors -->
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">Doctor Workloads</h2>
            <div id="doctors-container" class="mt-4 flex flex-col gap-3">
                <div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Loading doctors...</div>
            </div>
        </article>
    </div>

    <!-- Alert Toast (Hidden by default) -->
    <div id="toast-message" class="fixed bottom-5 right-5 z-50 hidden rounded-xl bg-green-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300">
        Action successful!
    </div>
</x-layout.app>