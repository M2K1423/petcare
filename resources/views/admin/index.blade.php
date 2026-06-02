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
</head>
<body data-page="admin-layout">
    <div id="app"></div>
</body>
</html>
