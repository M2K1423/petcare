# Admin Management System Documentation

## Overview

A comprehensive admin management system for the petCare veterinary clinic application covering 15 major features including user management, doctor management, services, inventory, payments, reports, and more.

## Architecture

### Controllers (12 Total)

#### 1. UserController (`/admin/users`)

**Responsibilities:** User account management, role assignment, account locking/unlocking

**Endpoints:**

- `GET /admin/users` - List all users with filters (role, search)
- `POST /admin/users` - Create new user
- `GET /admin/users/{user}` - View user details
- `PUT /admin/users/{user}` - Update user info
- `DELETE /admin/users/{user}` - Delete user
- `POST /admin/users/{user}/lock` - Lock user account
- `POST /admin/users/{user}/unlock` - Unlock user account
- `POST /admin/users/{user}/reset-password` - Reset password
- `POST /admin/users/{user}/assign-role` - Assign/change user role

**Supported Roles:** Admin, Vet, Receptionist, Cashier, Technician, Owner

#### 2. DoctorController (`/admin/doctors`)

**Responsibilities:** Doctor profile management, specialty, experience tracking

**Endpoints:**

- `GET /admin/doctors` - List all doctors
- `POST /admin/doctors` - Create doctor profile
- `GET /admin/doctors/{doctor}` - View doctor details
- `PUT /admin/doctors/{doctor}` - Update doctor info
- `DELETE /admin/doctors/{doctor}` - Delete doctor
- `GET /admin/doctors/specialties/list` - List all specialties

**Doctor Fields:** License number, specialty, years of experience, contact info, active status

#### 3. ServiceController (`/admin/services`)

**Responsibilities:** Service catalog management, pricing, availability

**Endpoints:**

- `GET /admin/services` - List services
- `POST /admin/services` - Create service
- `GET /admin/services/{service}` - View service
- `PUT /admin/services/{service}` - Update service
- `DELETE /admin/services/{service}` - Delete service
- `POST /admin/services/{service}/toggle` - Enable/disable service
- `PATCH /admin/services/{service}/price` - Update price

**Services:** Khám tổng quát, Tiêm phòng, Xét nghiệm, Spa/Grooming, Phẫu thuật

#### 4. MedicineController (`/admin/medicines`)

**Responsibilities:** Medicine/drug inventory, stock tracking, pricing

**Endpoints:**

- `GET /admin/medicines` - List medicines
- `POST /admin/medicines` - Add medicine
- `GET /admin/medicines/{medicine}` - View medicine
- `PUT /admin/medicines/{medicine}` - Update medicine
- `DELETE /admin/medicines/{medicine}` - Delete medicine
- `PATCH /admin/medicines/{medicine}/stock` - Update stock (add/subtract/set)
- `GET /admin/medicines/low-stock/list` - Low stock alert
- `GET /admin/medicines/expiring/list` - Expiration alert
- `GET /admin/medicines/categories/list` - Get all categories

**Tracked Fields:** SKU, category, unit, quantity, price, expiration date, image

#### 5. InventoryController (`/admin/inventory`)

**Responsibilities:** Warehouse operations, stock import/export

**Endpoints:**

- `GET /admin/inventory` - Inventory list
- `POST /admin/inventory/import` - Import stock (từ nhà cung cấp)
- `POST /admin/inventory/export` - Export stock (cho điều trị hoặc bán)
- `GET /admin/inventory/value` - Calculate inventory value
- `GET /admin/inventory/low-stock` - Low stock alert
- `GET /admin/inventory/expiration-alert` - Expiration alerts (Upcoming & Expired)
- `GET /admin/inventory/report` - Inventory report

#### 6. PaymentController (`/admin/payments`)

**Responsibilities:** Payment management, transaction confirmation, refunds

**Endpoints:**

- `GET /admin/payments` - List payments
- `GET /admin/payments/{payment}` - View payment
- `POST /admin/payments/{payment}/confirm` - Confirm payment
- `POST /admin/payments/{payment}/refund` - Refund payment
- `GET /admin/payments/stats/summary` - Payment statistics
- `GET /admin/payments/pending/list` - Pending payments

**Payment Status:** pending, completed, refunded

#### 7. AppointmentController (`/admin/appointments`)

**Responsibilities:** Global appointment management, scheduling, doctor assignment

**Endpoints:**

- `GET /admin/appointments` - List all appointments
- `GET /admin/appointments/{appointment}` - View appointment
- `POST /admin/appointments/{appointment}/assign-doctor` - Assign doctor
- `PATCH /admin/appointments/{appointment}/status` - Update status
- `PATCH /admin/appointments/{appointment}/reschedule` - Reschedule
- `POST /admin/appointments/{appointment}/cancel` - Cancel appointment
- `GET /admin/appointments/today/list` - Today's appointments
- `GET /admin/appointments/upcoming/list` - Upcoming appointments

#### 8. PetController (`/admin/pets`)

**Responsibilities:** Global pet management, health record overview

**Endpoints:**

- `GET /admin/pets` - List all pets
- `GET /admin/pets/{pet}` - View pet details
- `GET /admin/pets/{petId}/owner` - Get owner's pets
- `GET /admin/pets/{pet}/appointments` - Pet's appointment history
- `GET /admin/pets/{pet}/health-records` - Pet's medical records
- `GET /admin/pets/stats/summary` - Pet statistics

#### 9. ReportController (`/admin/reports`)

**Responsibilities:** Analytics and business intelligence

**Endpoints:**

- `GET /admin/reports/appointments` - Appointment statistics
- `GET /admin/reports/revenue` - Revenue reports (day/month/year)
- `GET /admin/reports/doctor-performance` - Doctor performance metrics
- `GET /admin/reports/service-popularity` - Popular services
- `GET /admin/reports/customers` - Customer statistics
- `GET /admin/reports/top-services` - Top services

#### 10. DashboardController (`/admin/dashboard`)

**Responsibilities:** Admin home page, key metrics, alerts

**Endpoints:**

- `GET /admin/dashboard` - Main dashboard
- `GET /admin/dashboard/stats` - Dashboard statistics by period (week/month/year)

**Dashboard Components:**

- Summary: Total users, doctors, pets, appointments
- Today stats: Appointments, revenue, new customers
- Revenue: Today, this month, this year, all-time
- Alerts: Low stock, expiring medicines, pending payments, locked users
- Recent activity: Latest 10 activities

#### 11. SettingController (`/admin/settings`)

**Responsibilities:** System configuration and clinic settings

**Endpoints:**

- `GET /admin/settings` - List all settings
- `GET /admin/settings/{key}` - Get specific setting
- `PUT /admin/settings/{key}` - Update setting
- `POST /admin/settings/bulk/update` - Bulk update
- `GET /admin/settings/clinic/settings` - Get clinic settings
- `POST /admin/settings/clinic/settings` - Update clinic settings

**Key Settings:**

- Clinic info: name, phone, email, address
- Working hours: start time, end time
- Appointments: slot duration, advance booking days, notice period
- Payments: online payment enabled, deposit percentage
- Notifications: appointment reminders enabled, reminder hours before

#### 12. ActivityLogController (`/admin/logs`)

**Responsibilities:** Audit trail, user activity tracking

**Endpoints:**

- `GET /admin/logs` - List all activity logs
- `GET /admin/logs/{activityLog}` - View activity log
- `GET /admin/logs/user/{userId}` - User activity history
- `GET /admin/logs/entity/{entityType}/{entityId}` - Entity change history
- `GET /admin/logs/summary/audit` - Audit summary
- `DELETE /admin/logs/clear/old` - Delete old logs

**Logged Actions:** create, update, delete, login, logout, lock, unlock, reset_password, assign_role, etc.

## Models & Database

### New Models

#### ActivityLog

Tracks all system changes and user actions

```
- user_id (FK to User)
- action (string): create, update, delete, login, logout
- entity_type (string): User, Doctor, Service, Medicine, Payment, etc.
- entity_id (integer)
- old_values (JSON)
- new_values (JSON)
- ip_address (string)
- user_agent (string)
- description (text)
```

#### SystemSetting

Stores configurable system settings

```
- key (string, unique)
- value (longText)
- data_type (string): string, integer, boolean, json
- description (text)
```

### Extended Models

**User** (added fields):

- `is_locked` (boolean)
- `locked_at` (timestamp)
- `last_login_at` (timestamp)

**All models** have relationships to ActivityLog for audit trail

## Policies

### Policy Classes

- `UserPolicy` - User CRUD permissions
- `DoctorPolicy` - Doctor management permissions
- `ServicePolicy` - Service management permissions
- `MedicinePolicy` - Medicine CRUD permissions
- `PaymentPolicy` - Payment confirmation/refund permissions

**Access Control:** All admin policies check `user->hasRole('admin')` as primary gate

## Authentication & Authorization

### Role-Based Access Control

```php
// All admin routes require admin role
Route::middleware('role:admin')->group(function (): void {
    // Admin routes here
});
```

### Policy Authorization

```php
$this->authorize('create', User::class);
$this->authorize('update', $user);
```

## Activity Logging

### Automatic Logging

Activity logging happens in three ways:

1. **Manual in Controllers:**

```php
ActivityLog::log(
    auth()->id(),
    'create',
    'User',
    $user->id,
    [],
    $user->only(['name', 'email', 'role_id']),
    "Created user: {$user->name}"
);
```

2. **Login/Logout Middleware:**

- Logs every login with IP and timestamp
- Logs every logout
- Updates `last_login_at` field

3. **Query Auditing:**
   Each change records:

- Who made it (user_id)
- When (timestamps)
- What changed (old_values → new_values)
- Why (description)

## API Response Format

### Pagination (for list endpoints)

```json
{
    "data": [...],
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    },
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### Error Response

```json
{
    "message": "Unauthorized",
    "errors": {...}
}
```

## Seeding

### AdminSeeder

Initializes admin system with:

1. **Roles:** Admin, Vet, Receptionist, Cashier, Technician, Owner
2. **Demo Users:** One per role for testing
3. **Services:** 5 predefined services
4. **Doctor Profile:** One demo doctor
5. **System Settings:** Default clinic settings

**Run seeder:**

```bash
php artisan db:seed --class=AdminSeeder
```

**Demo Credentials:**

- Admin: admin@petcare.local / password
- Doctor: doctor1@petcare.local / password
- Receptionist: receptionist1@petcare.local / password

## Key Features

### 1. User Management

- Create/Edit/Delete users
- Lock/Unlock accounts
- Reset passwords
- Assign roles with audit trail
- Filter by role or search

### 2. Doctor Management

- Maintain doctor profiles
- Track specialties
- Experience levels
- Availability status

### 3. Service Management

- Create/edit/delete services
- Update pricing
- Toggle availability
- Track service usage

### 4. Inventory Management

- Import stock from suppliers
- Export stock for treatment or sale
- Track stock levels
- Monitor expiration dates
- Low stock alerts
- Calculate inventory value

### 5. Payment Management

- Confirm/refund payments
- View transaction history
- Payment statistics
- Pending payment alerts
- Multi-gateway support

### 6. Appointments

- View all appointments
- Assign doctors
- Reschedule appointments
- Cancel with reason
- View today's appointments
- Filter by status, doctor, owner, date

### 7. Reporting & Analytics

- Appointment statistics
- Revenue reports (daily/monthly/yearly)
- Doctor performance
- Service popularity
- Customer statistics
- Top services

### 8. System Configuration

- Clinic information
- Working hours
- Appointment policies
- Payment settings
- Notification settings

### 9. Audit Logs

- Track all changes
- View change history
- User activity history
- Entity change history
- Export audit reports

### 10. Dashboard

- Key metrics at a glance
- Revenue summary
- Today's activities
- System alerts
- Recent activity feed
- Charts/graphs data

## Query Examples

### Get users by role

```
GET /api/admin/users?role=receptionist
```

### Get low stock medicines

```
GET /api/admin/medicines/low-stock/list?threshold=5
```

### Get revenue for date range

```
GET /api/admin/reports/revenue?date_from=2026-04-01&date_to=2026-04-30
```

### Get doctor's appointments

```
GET /api/admin/appointments?doctor_id=1&date_from=2026-04-20
```

### Get audit trail for user

```
GET /api/admin/logs/entity/User/5
```

## Best Practices

1. **Always check authorization:**

    ```php
    $this->authorize('action', $model);
    ```

2. **Log significant actions:**

    ```php
    ActivityLog::log(auth()->id(), 'action', 'Entity', $id, $old, $new, 'description');
    ```

3. **Validate input:**

    ```php
    $validated = $request->validate([...]);
    ```

4. **Handle locked users:**

    ```php
    if ($user->is_locked) {
        return response()->json(['message' => 'User account is locked'], 423);
    }
    ```

5. **Use pagination for lists:**
    ```php
    $per_page = $request->input('per_page', 15);
    $items = Model::paginate($per_page);
    ```

## Testing

### Create admin user for testing

```bash
php artisan tinker

$admin = User::where('email', 'admin@petcare.local')->first();
$token = $admin->createToken('admin-token')->plainTextToken;
```

### Test endpoints with Bearer token

```bash
curl -H "Authorization: Bearer $token" \
     https://petcare.local/api/admin/users
```

## Migration & Deployment

### Run migrations

```bash
php artisan migrate
```

### Seed admin data

```bash
php artisan db:seed --class=AdminSeeder
```

### Clear settings cache (after manual DB updates)

```php
SystemSetting::clearCache();
```

## Troubleshooting

### "Unauthorized" response

- Check user has admin role
- Verify token is valid
- Check policy allows action

### Missing audit logs

- Verify ActivityLog migration ran
- Check middleware is registered
- Verify LogActivity middleware is active

### Settings not updating

- Clear settings cache: `SystemSetting::clearCache()`
- Verify setting key exists
- Check data_type matches value format

## Future Enhancements

1. Batch operations (delete multiple, export)
2. Scheduled reports (email daily/weekly)
3. User impersonation for support
4. Two-factor authentication
5. IP whitelist for admin access
6. Backup/restore functionality
7. Advanced analytics/dashboards
8. API rate limiting per user
9. Custom email templates
10. Multi-language support
