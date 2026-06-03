<x-layout.app
    title="Chi tiết sản phẩm thuốc | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-medicine-detail" data-medicine-id="{{ $medicineId }}"></div>
</x-layout.app>
