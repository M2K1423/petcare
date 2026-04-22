@props([
    'title' => config('app.name', 'PetCare'),
    'vite' => ['resources/css/app.css'],
    'page' => null,
    'showHeader' => true,
    'showFooter' => false,
    'showSidebar' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite($vite)
</head>
<body data-page="{{ $page }}" class="pc-day relative flex min-h-screen flex-col overflow-x-hidden text-slate-900">
    <div class="pc-haze" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-a" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-b" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-c" aria-hidden="true"></div>

    @if ($showHeader)
        <x-layout.header />
    @endif

    <main class="relative z-10 w-full flex-1 px-4 py-6 md:px-8 md:py-8 {{ $showSidebar ? 'max-w-none' : 'mx-auto max-w-6xl' }}">
        @if ($showSidebar)
            <x-layout.sidebar />
            <div class="min-w-0 flex-1 lg:pl-72">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </main>
</body>
</html>
