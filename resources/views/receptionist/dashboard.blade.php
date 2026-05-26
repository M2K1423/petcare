<x-layout.app
    title="Bảng điều khiển lễ tân | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="receptionist-dashboard"></div>
</x-layout.app>