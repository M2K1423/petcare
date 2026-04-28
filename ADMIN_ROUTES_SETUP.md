# Admin Frontend Web Routes Setup

Để tích hợp tất cả các Vue components vào Laravel, thêm các routes này vào `routes/web.php`:

## Step 1: Thêm Admin Routes

Thêm sau các route hiện tại:

```php
<?php
// routes/web.php

// ... existing routes ...

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Require admin role middleware
    Route::middleware('role:admin')->group(function () {
        // Main layout
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        })->name('index');

        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // User Management
        Route::view('/users', 'admin.users')->name('users');

        // Doctor Management
        Route::view('/doctors', 'admin.doctors')->name('doctors');

        // Service Management
        Route::view('/services', 'admin.services')->name('services');

        // Medicine Management
        Route::view('/medicines', 'admin.medicines')->name('medicines');

        // Pet Management
        Route::view('/pets', 'admin.pets')->name('pets');

        // Appointment Management
        Route::view('/appointments', 'admin.appointments')->name('appointments');

        // Payment Management
        Route::view('/payments', 'admin.payments')->name('payments');

        // Inventory Management
        Route::view('/inventory', 'admin.inventory')->name('inventory');

        // Reports & Analytics
        Route::view('/reports', 'admin.reports')->name('reports');

        // Settings
        Route::view('/settings', 'admin.settings')->name('settings');

        // Activity Logs
        Route::view('/logs', 'admin.logs')->name('logs');
    });
});
```

## Step 2: Middleware Setup

Đảm bảo bạn có middleware `role:admin`. Nó phải kiểm tra:

```php
<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!auth()->user()->hasRole($role)) {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
```

Đăng ký trong `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... other middleware
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

## Step 3: Authentication

Đảm bảo trước khi truy cập admin:

1. User phải đăng nhập (`@auth` middleware)
2. User phải có role 'admin' (`role:admin` middleware)
3. User phải có email verified (`verified` middleware - tuỳ chọn)

## Step 4: Vue Component Integration

Cấu trúc `data-page` attribute cho mỗi blade view:

```html
<!-- resources/views/admin/dashboard.blade.php -->
<div data-page="admin-dashboard"><!-- Vue app mounts here --></div>

<!-- resources/views/admin/users.blade.php -->
<div data-page="admin-users"><!-- Vue app mounts here --></div>

<!-- ... etc cho tất cả trang ... -->
```

App loader trong `resources/js/app.js` sẽ:

1. Đọc `data-page` attribute từ body
2. Tìm tương ứng component trong `pageLoaders`
3. Import component động
4. Mount Vue app

## Step 5: API Authentication

Tất cả Vue components sử dụng JWT Bearer token:

```javascript
// Trong mỗi Vue component
const token = localStorage.getItem("token");

const res = await fetch("/api/admin/users", {
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    },
});
```

Đảm bảo token được lưu trong `localStorage` sau khi đăng nhập:

```javascript
// Sau khi đăng nhập thành công
const loginResponse = await fetch("/api/auth/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password }),
});

const data = await loginResponse.json();
localStorage.setItem("token", data.token);
```

## Step 6: Verify All Routes Work

```bash
# Test admin dashboard
curl http://localhost:8000/admin/dashboard \
  -H "Cookie: XSRF-TOKEN=...; laravel_session=..."

# List all routes
php artisan route:list | grep admin

# Check admin middleware is registered
php artisan route:list | grep "role:admin"
```

## Routes Summary

| URL                   | View               | Component              | Tính Năng           |
| --------------------- | ------------------ | ---------------------- | ------------------- |
| `/admin/dashboard`    | admin.dashboard    | admin-dashboard.vue    | Dashboard tổng quát |
| `/admin/users`        | admin.users        | admin-users.vue        | Quản lý người dùng  |
| `/admin/doctors`      | admin.doctors      | admin-doctors.vue      | Quản lý bác sĩ      |
| `/admin/services`     | admin.services     | admin-services.vue     | Quản lý dịch vụ     |
| `/admin/medicines`    | admin.medicines    | admin-medicines.vue    | Quản lý thuốc       |
| `/admin/pets`         | admin.pets         | admin-pets.vue         | Quản lý thú cưng    |
| `/admin/appointments` | admin.appointments | admin-appointments.vue | Quản lý lịch hẹn    |
| `/admin/payments`     | admin.payments     | admin-payments.vue     | Quản lý thanh toán  |
| `/admin/inventory`    | admin.inventory    | admin-inventory.vue    | Quản lý kho         |
| `/admin/reports`      | admin.reports      | admin-reports.vue      | Báo cáo thống kê    |
| `/admin/settings`     | admin.settings     | admin-settings.vue     | Cấu hình hệ thống   |
| `/admin/logs`         | admin.logs         | admin-logs.vue         | Nhật ký hoạt động   |

## Optional: Protect with Two-Factor Auth

```php
Route::middleware(['auth', 'verified', '2fa'])->prefix('admin')->group(function () {
    // Admin routes...
});
```

## Navigation Link

Thêm link vào navbar/layout để dễ dàng truy cập:

```html
@if(auth()->user()->hasRole('admin'))
<a href="{{ route('admin.dashboard') }}" class="nav-link"> Admin Panel </a>
@endif
```

## Testing

```php
// tests/Feature/AdminRouteTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role_id' => 1]); // Assuming 1 = admin

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role_id' => 6]); // Assuming 6 = owner

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_admin()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }
}
```

## Troubleshooting

### Problem: "role:admin middleware not found"

**Solution:** Kiểm tra middleware đã được đăng ký trong `config/auth.php` hoặc `app/Http/Kernel.php`

### Problem: Vue components không load

**Solution:** Kiểm tra `data-page` attribute khớp với key trong `pageLoaders` object

### Problem: API 401 Unauthorized

**Solution:** Kiểm tra token được lưu và gửi đúng trong Authorization header

### Problem: CSRF token mismatch

**Solution:** Thêm `@csrf` directive trong form hoặc gửi XSRF-TOKEN header

## Performance Tips

1. **Lazy load Vue components** - ✓ Đã implemented với dynamic imports
2. **Cache API responses** - Thêm caching layer (Redis)
3. **Pagination** - Tất cả danh sách đều có pagination
4. **Debounce search** - Thêm debounce vào search inputs
5. **Minify assets** - `npm run build` cho production

## Security Best Practices

1. ✓ Rate limiting trên API endpoints
2. ✓ Token expiration
3. ✓ CSRF protection
4. ✓ Input validation
5. ✓ SQL injection prevention (Eloquent)
6. ✓ XSS prevention
7. ✓ Role-based access control
8. ✓ Audit logging cho tất cả changes

Tất cả đã được implement trong backend!
