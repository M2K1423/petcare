<x-layout.app
    title="Hồ sơ sức khỏe thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-pet-health-records" data-pet-id="{{ $petId }}"></div>
</x-layout.app>
