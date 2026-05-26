<x-layout.app
    title="Lịch hẹn bác sĩ | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="vet-appointments"></div>
</x-layout.app>
