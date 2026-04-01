<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Firebase Auth | {{ config('app.name', 'PetCare') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="pc-page">
    <main class="pc-shell">
        <div id="firebase-auth-app"></div>
    </main>
</body>
</html>
