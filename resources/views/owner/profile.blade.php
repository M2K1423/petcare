<x-layout.app
    title="Hồ sơ chủ nuôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-profile"></div>
</x-layout.app>
