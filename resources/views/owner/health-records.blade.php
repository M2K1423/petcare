<x-layout.app
    title="Hồ sơ sức khỏe & bệnh án | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-health-records"></div>
</x-layout.app>
