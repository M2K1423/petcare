<x-layout.app
    title="Giỏ hàng của tôi | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-cart"></div>
</x-layout.app>
