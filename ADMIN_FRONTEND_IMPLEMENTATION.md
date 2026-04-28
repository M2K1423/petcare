# Admin Frontend Implementation Guide

Toàn bộ frontend admin system đã được hoàn thành với 12 Vue components tích hợp đầy đủ với backend API.

## 📋 Danh Sách Các Trang Admin

### 1. **Admin Layout** (`admin-layout.vue`)

- Navigation sidebar với links tới tất cả trang admin
- User info display
- Logout functionality
- Router view cho content

### 2. **Admin Dashboard** (`admin-dashboard.vue`)

- 4 Summary Cards: Tổng người dùng, bác sĩ, thú cưng, lịch hẹn
- Revenue Display: Hôm nay, tháng, năm, tổng cộng
- Alerts System: Hàng hết, sắp hết hạn, chờ xác nhận, người dùng bị khóa
- Recent Activity Feed: 10 hoạt động gần đây
- Refresh button

### 3. **User Management** (`admin-users.vue`)

- Search + Role filter
- Create/Edit User modal
- CRUD operations: Create, Read, Update, Delete
- Lock/Unlock user account
- Reset password functionality
- Pagination (10 per page)
- Full audit logging integration

### 4. **Doctor Management** (`admin-doctors.vue`)

- Search + Grid/Card view
- Create/Edit doctor form
- Fields: License Number, Specialty, Years of Experience, Phone, Email
- Delete functionality
- Status badge (Active/Inactive)

### 5. **Service Management** (`admin-services.vue`)

- Search + Service list
- Create/Edit service modal
- Toggle active/inactive status
- Fields: Name, Description, Price, Duration
- Delete functionality
- Responsive grid layout

### 6. **Medicine Management** (`admin-medicines.vue`)

- Search + Category filter
- Stock alerts: Low stock, Expiring, Expired medicines
- Create/Edit modal with all fields
- Update stock quantity with action (add/subtract/set)
- Color-coded expiration dates
- Delete functionality

### 7. **Pet Management** (`admin-pets.vue`)

- Search + Species filter
- Pet card list with owner info
- View pet details modal
- Links to appointments and health records
- Basic CRUD structure

### 8. **Appointment Management** (`admin-appointments.vue`)

- Global appointment management
- Search + Status filter
- Today's stats: Total, Completed, Scheduled, Cancelled
- Assign doctor to appointment
- Reschedule appointment
- Cancel with reason
- Status badges with color coding

### 9. **Payment Management** (`admin-payments.vue`)

- Search + Status filter
- Payment statistics: Pending, Completed, Failed, Refunded
- Confirm pending payments
- Refund completed payments
- View payment details modal
- Transaction tracking

### 10. **Inventory Management** (`admin-inventory.vue`)

- Import/Export functionality (CSV)
- Total inventory value calculation
- Low stock alerts
- Expiring medicine alerts
- Detailed inventory table
- Stock status indicators

### 11. **Reports & Analytics** (`admin-reports.vue`)

- Multiple report types:
    - Appointments: Total, Completed, Scheduled, Cancelled
    - Revenue: Total, Completed, Pending, Refunded
    - Doctor Performance: Appointments, Revenue, Rating
    - Service Popularity: Usage count, Revenue
    - Customer Statistics: Visits, Spending, Pets
- Date range filter
- Statistics cards with visual indicators

### 12. **Settings Management** (`admin-settings.vue`)

- Clinic Information: Name, Address, Phone, Email, Description
- Working Hours: Weekday and Weekend schedule
- Appointment Settings: Slot duration, Max per day, Online booking
- Payment Settings: Methods, Bank account info
- Notification Settings: Reminders, Timing
- System Settings: Activity logging, Backup
- Save/Reset functionality

### 13. **Activity Logs** (`admin-logs.vue`)

- Search + Action filter + Entity filter
- Activity statistics: Total, Today, This week, This month
- Log details modal with old/new values
- JSON diff view for changes
- IP address and User Agent tracking
- Clear old logs functionality
- Pagination (20 per page)

## 🚀 Cách Sử Dụng

### Truy Cập Các Trang

```
GET /admin/dashboard          -> admin-dashboard.vue
GET /admin/users              -> admin-users.vue
GET /admin/doctors            -> admin-doctors.vue
GET /admin/services           -> admin-services.vue
GET /admin/medicines          -> admin-medicines.vue
GET /admin/pets               -> admin-pets.vue
GET /admin/appointments       -> admin-appointments.vue
GET /admin/payments           -> admin-payments.vue
GET /admin/inventory          -> admin-inventory.vue
GET /admin/reports            -> admin-reports.vue
GET /admin/settings           -> admin-settings.vue
GET /admin/logs               -> admin-logs.vue
```

### Laravel Routes

Thêm vào `routes/web.php`:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard');
    Route::view('/users', 'admin.users');
    Route::view('/doctors', 'admin.doctors');
    Route::view('/services', 'admin.services');
    Route::view('/medicines', 'admin.medicines');
    Route::view('/pets', 'admin.pets');
    Route::view('/appointments', 'admin.appointments');
    Route::view('/payments', 'admin.payments');
    Route::view('/inventory', 'admin.inventory');
    Route::view('/reports', 'admin.reports');
    Route::view('/settings', 'admin.settings');
    Route::view('/logs', 'admin.logs');
});
```

## 📊 API Endpoints Integration

Tất cả các trang được tích hợp với backend API:

### Users API

```
GET     /api/admin/users
POST    /api/admin/users
PUT     /api/admin/users/{id}
DELETE  /api/admin/users/{id}
POST    /api/admin/users/{id}/lock
POST    /api/admin/users/{id}/unlock
POST    /api/admin/users/{id}/reset-password
```

### Doctors API

```
GET     /api/admin/doctors
POST    /api/admin/doctors
PUT     /api/admin/doctors/{id}
DELETE  /api/admin/doctors/{id}
GET     /api/admin/doctors/{id}/specialties
```

### Services API

```
GET     /api/admin/services
POST    /api/admin/services
PUT     /api/admin/services/{id}
DELETE  /api/admin/services/{id}
POST    /api/admin/services/{id}/toggle
PATCH   /api/admin/services/{id}/price
```

### Medicines API

```
GET     /api/admin/medicines
POST    /api/admin/medicines
PUT     /api/admin/medicines/{id}
DELETE  /api/admin/medicines/{id}
PATCH   /api/admin/medicines/{id}/stock
GET     /api/admin/medicines/alerts
GET     /api/admin/medicines/expiring
```

### Appointments API

```
GET     /api/admin/appointments
POST    /api/admin/appointments/{id}/assign-doctor
POST    /api/admin/appointments/{id}/reschedule
POST    /api/admin/appointments/{id}/cancel
GET     /api/admin/appointments/today
GET     /api/admin/appointments/upcoming
```

### Payments API

```
GET     /api/admin/payments
POST    /api/admin/payments/{id}/confirm
POST    /api/admin/payments/{id}/refund
GET     /api/admin/payments/stats
GET     /api/admin/payments/pending
```

### Inventory API

```
GET     /api/admin/inventory
POST    /api/admin/inventory/import
GET     /api/admin/inventory/export
GET     /api/admin/inventory/value
GET     /api/admin/inventory/alerts
```

### Reports API

```
GET     /api/admin/reports/appointments
GET     /api/admin/reports/revenue
GET     /api/admin/reports/doctor-performance
GET     /api/admin/reports/service-popularity
GET     /api/admin/reports/customers
GET     /api/admin/reports/top-services
```

### Settings API

```
GET     /api/admin/settings
POST    /api/admin/settings
PUT     /api/admin/settings/{key}
DELETE  /api/admin/settings/{key}
```

### Activity Logs API

```
GET     /api/admin/logs
GET     /api/admin/logs/{id}
GET     /api/admin/logs/user/{user_id}
GET     /api/admin/logs/entity/{entity_type}/{entity_id}
POST    /api/admin/logs/clear-old
GET     /api/admin/logs/audit-summary
```

### Dashboard API

```
GET     /api/admin/dashboard
GET     /api/admin/dashboard/stats
```

## 🎨 Styling & Components

Tất cả các trang sử dụng:

- **Tailwind CSS** cho styling responsive
- **Vue 3 Composition API** với `<script setup>` syntax
- **Color-coded badges** cho trạng thái
- **Modal dialogs** cho CRUD operations
- **Search & filter** trên tất cả danh sách
- **Pagination** cho các danh sách lớn
- **Confirmation dialogs** cho hành động nguy hiểm

## 💾 Data Persistence

Tất cả dữ liệu được:

- Lưu trữ qua API calls tới backend
- Xác thực với JWT token từ `localStorage.getItem('token')`
- Có audit logging cho tất cả thay đổi
- Cập nhật real-time khi thành công

## 🔐 Authentication & Authorization

- Sử dụng Bearer token authentication
- Tất cả requests đều gửi Authorization header
- Backend sử dụng `role:admin` middleware
- Policy-based authorization trên từng resource

## 📝 Lưu Ý

1. **Token Management**: Đảm bảo token được lưu trong `localStorage` với key `'token'`
2. **API Base URL**: Tất cả requests sử dụng relative URLs (e.g., `/api/admin/...`)
3. **Error Handling**: Các lỗi được log tới browser console
4. **Responsive Design**: Tất cả components hoạt động trên mobile/tablet/desktop
5. **Loading States**: Thêm loading indicators khi cần thiết
6. **Validation**: Frontend validation + Backend validation

## 🔄 Tương Lai

Có thể cải thiện thêm:

- [ ] Add loading spinners during API calls
- [ ] Add toast notifications for success/error
- [ ] Implement real-time data sync (WebSocket)
- [ ] Add export to PDF/Excel for reports
- [ ] Implement advanced filtering & sorting
- [ ] Add user activity timeline
- [ ] Implement bulk operations
- [ ] Add role-based UI visibility
- [ ] Implement data validation
- [ ] Add service worker for offline support
