# Admin API Quick Reference

## Authentication

All admin endpoints require:

- `Authorization: Bearer {token}` header
- User must have `admin` role

Login: `POST /api/auth/login`

```json
{
    "email": "admin@petcare.local",
    "password": "password"
}
```

## User Management - `/api/admin/users`

| Method | Endpoint                           | Purpose                    |
| ------ | ---------------------------------- | -------------------------- |
| GET    | `/admin/users`                     | List users with pagination |
| GET    | `/admin/users?role=receptionist`   | Filter by role             |
| GET    | `/admin/users?search=john`         | Search by name/email       |
| POST   | `/admin/users`                     | Create new user            |
| GET    | `/admin/users/{id}`                | View user details          |
| PUT    | `/admin/users/{id}`                | Update user                |
| DELETE | `/admin/users/{id}`                | Delete user                |
| POST   | `/admin/users/{id}/lock`           | Lock account               |
| POST   | `/admin/users/{id}/unlock`         | Unlock account             |
| POST   | `/admin/users/{id}/reset-password` | Reset password             |
| POST   | `/admin/users/{id}/assign-role`    | Change role                |

**Create User:**

```json
POST /api/admin/users
{
  "name": "John Doe",
  "email": "john@petcare.local",
  "phone": "+84123456789",
  "password": "SecurePass123",
  "role_id": 2
}
```

## Doctor Management - `/api/admin/doctors`

| Method | Endpoint                          | Purpose             |
| ------ | --------------------------------- | ------------------- |
| GET    | `/admin/doctors`                  | List all doctors    |
| POST   | `/admin/doctors`                  | Create doctor       |
| GET    | `/admin/doctors/{id}`             | View doctor         |
| PUT    | `/admin/doctors/{id}`             | Update doctor       |
| DELETE | `/admin/doctors/{id}`             | Delete doctor       |
| GET    | `/admin/doctors/specialties/list` | Get all specialties |

**Create Doctor:**

```json
POST /api/admin/doctors
{
  "user_id": 5,
  "license_number": "LIC002",
  "specialty": "Cardiology",
  "years_of_experience": 8,
  "phone": "+84987654321",
  "email": "doctor@petcare.local"
}
```

## Service Management - `/api/admin/services`

| Method | Endpoint                      | Purpose        |
| ------ | ----------------------------- | -------------- |
| GET    | `/admin/services`             | List services  |
| POST   | `/admin/services`             | Create service |
| GET    | `/admin/services/{id}`        | View service   |
| PUT    | `/admin/services/{id}`        | Update service |
| DELETE | `/admin/services/{id}`        | Delete service |
| POST   | `/admin/services/{id}/toggle` | Enable/disable |
| PATCH  | `/admin/services/{id}/price`  | Update price   |

**Create Service:**

```json
POST /api/admin/services
{
  "name": "Dental Cleaning",
  "description": "Professional dental cleaning and checkup",
  "price": 350000,
  "duration_minutes": 45,
  "is_active": true
}
```

## Medicine Management - `/api/admin/medicines`

| Method | Endpoint                                                | Purpose          |
| ------ | ------------------------------------------------------- | ---------------- |
| GET    | `/admin/medicines`                                      | List medicines   |
| GET    | `/admin/medicines?low_stock=true&min_stock=10`          | Low stock filter |
| GET    | `/admin/medicines?expiring_soon=true&days_threshold=30` | Expiring soon    |
| POST   | `/admin/medicines`                                      | Add medicine     |
| GET    | `/admin/medicines/{id}`                                 | View medicine    |
| PUT    | `/admin/medicines/{id}`                                 | Update medicine  |
| DELETE | `/admin/medicines/{id}`                                 | Delete medicine  |
| PATCH  | `/admin/medicines/{id}/stock`                           | Update stock     |
| GET    | `/admin/medicines/low-stock/list`                       | Low stock alert  |
| GET    | `/admin/medicines/expiring/list`                        | Expiration alert |
| GET    | `/admin/medicines/categories/list`                      | Get categories   |

**Add Medicine:**

```json
POST /api/admin/medicines
{
  "name": "Amoxicillin 500mg",
  "sku": "AMX-500",
  "category": "Antibiotics",
  "unit": "tablet",
  "stock_quantity": 100,
  "price": 5000,
  "expiration_date": "2027-12-31",
  "image_url": "https://example.com/image.jpg"
}
```

**Update Stock:**

```json
PATCH /api/admin/medicines/5/stock
{
  "quantity": 50,
  "action": "add",
  "reason": "Stock import from supplier ABC"
}
```

## Inventory Management - `/api/admin/inventory`

| Method | Endpoint                            | Purpose          |
| ------ | ----------------------------------- | ---------------- |
| GET    | `/admin/inventory`                  | List inventory   |
| POST   | `/admin/inventory/import`           | Import stock     |
| POST   | `/admin/inventory/export`           | Export stock     |
| GET    | `/admin/inventory/value`            | Inventory value  |
| GET    | `/admin/inventory/low-stock`        | Low stock alert  |
| GET    | `/admin/inventory/expiration-alert` | Expiration alert |
| GET    | `/admin/inventory/report`           | Full report      |

**Import Stock:**

```json
POST /api/admin/inventory/import
{
  "medicine_id": 5,
  "quantity": 200,
  "supplier": "Pharma Company ABC",
  "cost_per_unit": 3000
}
```

**Export Stock:**

```json
POST /api/admin/inventory/export
{
  "medicine_id": 5,
  "quantity": 30,
  "export_type": "treatment",
  "notes": "For patient treatment"
}
```

## Payment Management - `/api/admin/payments`

| Method | Endpoint                                                  | Purpose          |
| ------ | --------------------------------------------------------- | ---------------- |
| GET    | `/admin/payments`                                         | List payments    |
| GET    | `/admin/payments?status=pending`                          | Filter by status |
| GET    | `/admin/payments?date_from=2026-04-01&date_to=2026-04-30` | Date range       |
| GET    | `/admin/payments/{id}`                                    | View payment     |
| POST   | `/admin/payments/{id}/confirm`                            | Confirm payment  |
| POST   | `/admin/payments/{id}/refund`                             | Refund payment   |
| GET    | `/admin/payments/stats/summary`                           | Payment stats    |
| GET    | `/admin/payments/pending/list`                            | Pending list     |

**Confirm Payment:**

```json
POST /api/admin/payments/10/confirm
{}
```

**Refund Payment:**

```json
POST /api/admin/payments/10/refund
{
  "reason": "Customer requested refund",
  "amount": 100000
}
```

## Appointment Management - `/api/admin/appointments`

| Method | Endpoint                                 | Purpose               |
| ------ | ---------------------------------------- | --------------------- |
| GET    | `/admin/appointments`                    | List all appointments |
| GET    | `/admin/appointments?date=2026-04-28`    | Filter by date        |
| GET    | `/admin/appointments?status=pending`     | Filter by status      |
| GET    | `/admin/appointments/{id}`               | View appointment      |
| POST   | `/admin/appointments/{id}/assign-doctor` | Assign doctor         |
| PATCH  | `/admin/appointments/{id}/status`        | Update status         |
| PATCH  | `/admin/appointments/{id}/reschedule`    | Reschedule            |
| POST   | `/admin/appointments/{id}/cancel`        | Cancel                |
| GET    | `/admin/appointments/today/list`         | Today's list          |
| GET    | `/admin/appointments/upcoming/list`      | Upcoming list         |

**Assign Doctor:**

```json
POST /api/admin/appointments/15/assign-doctor
{
  "doctor_id": 3
}
```

**Reschedule:**

```json
PATCH /api/admin/appointments/15/reschedule
{
  "appointment_at": "2026-05-01 10:30:00"
}
```

**Cancel:**

```json
POST /api/admin/appointments/15/cancel
{
  "reason": "Doctor unavailable"
}
```

## Pet Management - `/api/admin/pets`

| Method | Endpoint                          | Purpose              |
| ------ | --------------------------------- | -------------------- |
| GET    | `/admin/pets`                     | List all pets        |
| GET    | `/admin/pets/{id}`                | View pet details     |
| GET    | `/admin/pets/{id}/appointments`   | Pet's appointments   |
| GET    | `/admin/pets/{id}/health-records` | Pet's health records |
| GET    | `/admin/pets/stats/summary`       | Pet statistics       |

## Reports & Analytics - `/api/admin/reports`

| Method | Endpoint                                                         | Purpose           |
| ------ | ---------------------------------------------------------------- | ----------------- |
| GET    | `/admin/reports/appointments`                                    | Appointment stats |
| GET    | `/admin/reports/revenue`                                         | Revenue reports   |
| GET    | `/admin/reports/revenue?date_from=2026-04-01&date_to=2026-04-30` | Revenue period    |
| GET    | `/admin/reports/doctor-performance`                              | Doctor metrics    |
| GET    | `/admin/reports/service-popularity`                              | Popular services  |
| GET    | `/admin/reports/customers`                                       | Customer stats    |
| GET    | `/admin/reports/top-services?limit=5`                            | Top services      |

## System Settings - `/api/admin/settings`

| Method | Endpoint                          | Purpose              |
| ------ | --------------------------------- | -------------------- |
| GET    | `/admin/settings`                 | List all settings    |
| GET    | `/admin/settings/{key}`           | Get setting value    |
| PUT    | `/admin/settings/{key}`           | Update setting       |
| POST   | `/admin/settings/bulk/update`     | Bulk update          |
| GET    | `/admin/settings/clinic/settings` | Clinic config        |
| POST   | `/admin/settings/clinic/settings` | Update clinic config |

**Update Single Setting:**

```json
PUT /api/admin/settings/clinic_name
{
  "value": "My PetCare Clinic",
  "data_type": "string",
  "description": "Clinic name"
}
```

**Update Clinic Settings:**

```json
POST /api/admin/settings/clinic/settings
{
  "clinic_name": "My Clinic",
  "clinic_phone": "+84123456789",
  "working_hours_start": "07:00",
  "working_hours_end": "19:00",
  "appointment_slot_duration": 30,
  "deposit_policy_percentage": 20
}
```

## Activity Logs & Audit - `/api/admin/logs`

| Method | Endpoint                         | Purpose          |
| ------ | -------------------------------- | ---------------- |
| GET    | `/admin/logs`                    | List all logs    |
| GET    | `/admin/logs?action=create`      | Filter by action |
| GET    | `/admin/logs?entity_type=User`   | Filter by entity |
| GET    | `/admin/logs/{id}`               | View log entry   |
| GET    | `/admin/logs/user/{userId}`      | User activity    |
| GET    | `/admin/logs/entity/{type}/{id}` | Entity history   |
| GET    | `/admin/logs/summary/audit`      | Audit summary    |
| DELETE | `/admin/logs/clear/old?days=90`  | Delete old logs  |

**Query Examples:**

```
GET /api/admin/logs?user_id=5
GET /api/admin/logs?action=update&entity_type=User
GET /api/admin/logs/entity/User/5
GET /api/admin/logs/user/3
```

## Dashboard - `/api/admin/dashboard`

| Method | Endpoint                              | Purpose        |
| ------ | ------------------------------------- | -------------- |
| GET    | `/admin/dashboard`                    | Main dashboard |
| GET    | `/admin/dashboard/stats?period=week`  | Weekly stats   |
| GET    | `/admin/dashboard/stats?period=month` | Monthly stats  |
| GET    | `/admin/dashboard/stats?period=year`  | Yearly stats   |

## Pagination & Filtering

**List with Pagination:**

```
GET /api/admin/users?page=2&per_page=20
```

**Search:**

```
GET /api/admin/users?search=john
GET /api/admin/medicines?search=Amox
```

**Multiple Filters:**

```
GET /api/admin/appointments?status=pending&doctor_id=3&date_from=2026-04-20
```

## Common HTTP Status Codes

| Code | Meaning                                    |
| ---- | ------------------------------------------ |
| 200  | OK - Success                               |
| 201  | Created - Resource created                 |
| 400  | Bad Request - Invalid input                |
| 401  | Unauthorized - Not authenticated           |
| 403  | Forbidden - Not authorized (no admin role) |
| 404  | Not Found - Resource doesn't exist         |
| 422  | Unprocessable Entity - Validation error    |
| 500  | Server Error                               |

## Error Response Format

```json
{
    "message": "The given data was invalid",
    "errors": {
        "email": ["The email has already been taken"],
        "password": ["The password must be at least 8 characters"]
    }
}
```

## Getting Started

1. **Login and get token:**

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@petcare.local",
    "password": "password"
  }'
```

2. **Use token in requests:**

```bash
curl http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

3. **Create first admin user (if needed):**

```bash
php artisan tinker
$role = Role::where('slug', 'admin')->first();
User::create([
  'name' => 'Admin',
  'email' => 'admin@example.com',
  'password' => bcrypt('password'),
  'role_id' => $role->id
]);
```

## Notes

- All timestamps are in UTC
- Pagination defaults to 15 items per page
- Most endpoints support `page` and `per_page` parameters
- Activity logs track all changes automatically
- Locked users cannot login
- Admin actions are logged with timestamps and IP addresses
