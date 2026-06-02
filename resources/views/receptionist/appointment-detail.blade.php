<x-layout.app
    title="Chi tiết lịch hẹn | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="receptionist-appointment-detail" data-appointment-id="{{ $appointmentId }}"></div>
</x-layout.app>
