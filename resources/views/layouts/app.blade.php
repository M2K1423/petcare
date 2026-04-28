@props(['page' => null])
<x-layout.app :page="$page ?? View::getSection('page')" :title="config('app.name', 'PetCare')" :vite="['resources/css/app.css', 'resources/js/app.js']" :showHeader="true" :showFooter="false" :showSidebar="true">
    @yield('content')
</x-layout.app>
