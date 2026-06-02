<x-layout.app
    title="Lịch hẹn của chủ nuôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-appointments"></div>
</x-layout.app>
