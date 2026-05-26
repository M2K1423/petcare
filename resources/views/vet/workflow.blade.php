<x-layout.app
    title="{{ $sectionTitle ?? 'Quy trình bác sĩ' }} | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    :showSidebar="true"
>
    <div data-page="vet-workflow"
        data-section-key="{{ $sectionKey ?? 'dashboard' }}"
        data-section-title="{{ $sectionTitle ?? 'Vet Workflow' }}"
        data-section-description="{{ $sectionDescription ?? '' }}"
    ></div>
</x-layout.app>
