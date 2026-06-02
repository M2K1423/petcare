<x-layout.app
    title="Chi tiết lịch hẹn bác sĩ | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="vet-appointment-detail" data-appointment-id="{{ $appointmentId }}"></div>
</x-layout.app>
