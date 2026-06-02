<x-layout.app
    title="Lịch theo tuần bác sĩ | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="vet-appointments-week"></div>
</x-layout.app>
