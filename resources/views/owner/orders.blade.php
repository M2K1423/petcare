<x-layout.app
    title="Đơn hàng của tôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-orders"></div>
</x-layout.app>
