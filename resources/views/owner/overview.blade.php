<x-layout.app
    title="Owner Overview | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="owner-overview"></div>
</x-layout.app>
