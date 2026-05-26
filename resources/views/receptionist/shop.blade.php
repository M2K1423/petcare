<x-layout.app 
    title="Cửa hàng thuốc | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="receptionist-shop"></div>
</x-layout.app>
