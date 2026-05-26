<x-layout.app
    title="Thú cưng của chủ nuôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-pets"></div>
</x-layout.app>
