<x-layout.app
    title="Lịch hẹn hôm nay | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="receptionist-appointments"></div>
</x-layout.app>