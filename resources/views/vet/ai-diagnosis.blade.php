<x-layout.app
    title="Chẩn đoán bệnh bằng AI | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="ai-diagnosis"></div>
</x-layout.app>
