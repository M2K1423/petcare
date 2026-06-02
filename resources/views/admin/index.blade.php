<!-- Admin Layout Wrapper -->
<!-- Sử dụng trang này làm layout chính cho tất cả admin pages -->
<!-- Nó sẽ hiển thị sidebar navigation + router-view cho content -->

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - PetCare</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
<body data-page="admin-layout">
    <div id="app"></div>
</body>
</html>
