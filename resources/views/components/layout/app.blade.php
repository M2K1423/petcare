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
    <script>
        window.Laravel = {
            broadcastConnection: "{{ config('broadcasting.default') }}",
            pusherKey: "{{ config('broadcasting.connections.pusher.key') }}",
            pusherCluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
            reverbKey: "{{ config('broadcasting.connections.reverb.key') }}",
            reverbHost: "{{ config('broadcasting.connections.reverb.options.host') }}",
            reverbPort: "{{ config('broadcasting.connections.reverb.options.port') }}",
            reverbScheme: "{{ config('broadcasting.connections.reverb.options.scheme') }}"
        };
    </script>
</head>
<body data-page="{{ $page }}" class="pc-day relative flex min-h-screen flex-col overflow-x-hidden text-slate-900">
    <div class="pc-haze" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-a" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-b" aria-hidden="true"></div>
    <div class="pc-ember pc-ember-c" aria-hidden="true"></div>

    <div id="page-loading-overlay" class="fixed inset-0 z-[100] flex items-center justify-center bg-white/75 px-4 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-4 rounded-3xl border border-[#DDE1E6] bg-white px-6 py-5 text-center shadow-[0_16px_40px_rgba(0,0,0,0.08)]">
            <div class="h-11 w-11 animate-spin rounded-full border-4 border-[#DDE1E6] border-t-[#2A6496]"></div>
            <div>
                <p class="text-sm font-semibold text-[#333333]">Đang tải...</p>
                <p class="mt-1 text-xs text-[#666666]">Vui lòng chờ trong giây lát</p>
            </div>
        </div>
    </div>

    @if ($showHeader)
        <x-layout.header />
    @endif

    @if ($showSidebar)
        <x-layout.sidebar />
    @endif

    <main class="relative z-10 w-full flex-1 px-4 py-6 md:px-8 md:py-8 {{ $showSidebar ? 'max-w-none' : 'mx-auto max-w-6xl' }}">
        @if ($showSidebar)
            <div class="min-w-0 flex-1 lg:pl-80">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </main>

    <script>
        (function () {
            const overlay = document.getElementById('page-loading-overlay');

            const hideOverlay = () => {
                if (!overlay) return;

                overlay.classList.add('opacity-0', 'pointer-events-none');
                window.setTimeout(() => overlay.remove(), 180);
            };

            if (document.readyState === 'complete') {
                hideOverlay();
            } else {
                window.addEventListener('load', hideOverlay, { once: true });
            }

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    hideOverlay();
                }
            });
        })();
    </script>
    
    @auth
        @if(in_array(auth()->user()->role->slug, ['owner', 'vet', 'receptionist', 'admin']) && !request()->routeIs('sanctum.auth') && !request()->routeIs('sanctum.auth.register'))
            <div id="chat-widget-root"></div>
        @endif
    @endauth
</body>
</html>
