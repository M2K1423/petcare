<x-layout.app
    title="Cửa hàng thú cưng | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-shop"></div>
</x-layout.app>
