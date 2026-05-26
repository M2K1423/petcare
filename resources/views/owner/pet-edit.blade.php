<x-layout.app
    title="Chỉnh sửa thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-pet-edit" data-pet-id="{{ $petId }}"></div>
</x-layout.app>