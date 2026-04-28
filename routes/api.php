<?php

use App\Http\Controllers\Api\Owner\AppointmentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Owner\PetHealthRecordController;
use App\Http\Controllers\Api\Owner\PetController;
use App\Http\Controllers\Api\Owner\SpeciesController;
use App\Http\Controllers\Api\Owner\MedicineController;
use App\Http\Controllers\Api\Owner\MedicineOrderController as OwnerMedicineOrderController;
use App\Http\Controllers\Api\Vet\AppointmentController as VetAppointmentController;
use App\Http\Controllers\Api\Receptionist\AppointmentController as ReceptionistAppointmentController;
use App\Http\Controllers\Api\Receptionist\CustomerController;
use App\Http\Controllers\Api\Receptionist\DoctorController;
use App\Http\Controllers\Api\Receptionist\MedicineOrderController as ReceptionistMedicineOrderController;
use App\Http\Controllers\Api\Receptionist\PaymentController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\Api\Auth\SanctumAuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\MedicineController as AdminMedicineController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\Admin\PetController as AdminPetController;
use App\Http\Controllers\Api\Admin\InventoryController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [SanctumAuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [SanctumAuthController::class, 'login'])->name('api.auth.login');
Route::get('/payments/vnpay/ipn', [VnpayController::class, 'ipn'])->name('api.payments.vnpay.ipn');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [SanctumAuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [SanctumAuthController::class, 'me'])->name('api.auth.me');
    Route::put('/auth/profile', [SanctumAuthController::class, 'updateProfile'])->name('api.auth.profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');

    Route::middleware('role:owner')->get('/owner/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Owner dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => Role::OWNER,
        ]);
    })->name('api.owner.dashboard');

    Route::middleware('role:owner')->group(function (): void {
        Route::get('/owner/species', [SpeciesController::class, 'index'])->name('api.owner.species.index');
        Route::apiResource('/owner/pets', PetController::class)->names('api.owner.pets');
        Route::get('/owner/pets/{pet}/health-records', [PetHealthRecordController::class, 'show'])->name('api.owner.pets.health-records.show');
        Route::get('/owner/appointments', [AppointmentController::class, 'index'])->name('api.owner.appointments.index');
        Route::post('/owner/appointments', [AppointmentController::class, 'store'])->name('api.owner.appointments.store');
        Route::delete('/owner/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('api.owner.appointments.destroy');
        Route::get('/owner/medicines', [MedicineController::class, 'index'])->name('api.owner.medicines.index');
        Route::get('/owner/medicine-orders', [OwnerMedicineOrderController::class, 'index'])->name('api.owner.medicine-orders.index');
        Route::post('/owner/medicine-orders', [OwnerMedicineOrderController::class, 'store'])->name('api.owner.medicine-orders.store');
    });

    Route::middleware('role:vet')->group(function (): void {
        Route::get('/vet/dashboard', function (Request $request) {
            return response()->json([
                'dashboard' => 'Vet dashboard',
                'user' => $request->user()?->only(['id', 'name', 'email']),
                'role' => Role::VET,
            ]);
        })->name('api.vet.dashboard');

        Route::get('/vet/dashboard-summary', [VetAppointmentController::class, 'dashboardSummary'])->name('api.vet.dashboard-summary');
        Route::get('/vet/appointments', [VetAppointmentController::class, 'index'])->name('api.vet.appointments.index');
        Route::get('/vet/appointments/{appointment}', [VetAppointmentController::class, 'show'])->name('api.vet.appointments.show');
        Route::patch('/vet/appointments/{appointment}/accept', [VetAppointmentController::class, 'acceptCase'])->name('api.vet.appointments.accept');
        Route::patch('/vet/appointments/{appointment}/workflow', [VetAppointmentController::class, 'updateWorkflowStatus'])->name('api.vet.appointments.workflow.update');
        Route::put('/vet/appointments/{appointment}/medical-record', [VetAppointmentController::class, 'saveMedicalRecord'])->name('api.vet.appointments.medical-record.save');
    });

    Route::middleware('role:receptionist')->group(function (): void {
        Route::get('/receptionist/species', [SpeciesController::class, 'index'])->name('api.receptionist.species.index');

        Route::get('/receptionist/dashboard', function (Request $request) {
            return response()->json([
                'dashboard' => 'Receptionist dashboard',
                'user' => $request->user()?->only(['id', 'name', 'email']),
                'role' => Role::RECEPTIONIST,
            ]);
        })->name('api.receptionist.dashboard');

        // Customer & Pet Walk-in
        Route::get('/receptionist/customers/search', [CustomerController::class, 'search'])->name('api.receptionist.customers.search');
        Route::post('/receptionist/customers/walk-in', [CustomerController::class, 'storeWalkIn'])->name('api.receptionist.customers.walk_in');

        // Appointments Management (Check-in, list, store)
        Route::get('/receptionist/appointments', [ReceptionistAppointmentController::class, 'index'])->name('api.receptionist.appointments.index');
        Route::get('/receptionist/appointments/{id}', [ReceptionistAppointmentController::class, 'show'])->name('api.receptionist.appointments.show');
        Route::post('/receptionist/appointments', [ReceptionistAppointmentController::class, 'store'])->name('api.receptionist.appointments.store');
        Route::patch('/receptionist/appointments/{id}/check-in', [ReceptionistAppointmentController::class, 'checkIn'])->name('api.receptionist.appointments.check_in');

        // Queue (Bảng điện tử phòng chờ & Ưu tiên)
        Route::get('/receptionist/queue', [ReceptionistAppointmentController::class, 'queue'])->name('api.receptionist.queue.index');
        Route::patch('/receptionist/appointments/{id}/emergency', [ReceptionistAppointmentController::class, 'markEmergency'])->name('api.receptionist.appointments.emergency');

        // Doctors (Điều phối)
        Route::get('/receptionist/doctors/available', [DoctorController::class, 'available'])->name('api.receptionist.doctors.available');

        // Payments / Billing
        Route::get('/receptionist/payments/unpaid', [PaymentController::class, 'unpaid'])->name('api.receptionist.payments.unpaid');
        Route::post('/receptionist/payments', [PaymentController::class, 'store'])->name('api.receptionist.payments.store');
        Route::get('/receptionist/medicine-orders', [ReceptionistMedicineOrderController::class, 'index'])->name('api.receptionist.medicine-orders.index');
        Route::patch('/receptionist/medicine-orders/{order}/confirm', [ReceptionistMedicineOrderController::class, 'confirm'])->name('api.receptionist.medicine-orders.confirm');
        Route::patch('/receptionist/medicine-orders/{order}/collect-payment', [ReceptionistMedicineOrderController::class, 'collect'])->name('api.receptionist.medicine-orders.collect-payment');
    });

    Route::middleware('role:admin')->group(function (): void {
        // Dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('api.admin.dashboard');
        Route::get('/admin/dashboard/stats', [DashboardController::class, 'stats'])->name('api.admin.dashboard.stats');

        // User Management
        Route::apiResource('/admin/users', AdminUserController::class)->names('api.admin.users');
        Route::post('/admin/users/{user}/lock', [AdminUserController::class, 'lock'])->name('api.admin.users.lock');
        Route::post('/admin/users/{user}/unlock', [AdminUserController::class, 'unlock'])->name('api.admin.users.unlock');
        Route::post('/admin/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('api.admin.users.reset-password');
        Route::post('/admin/users/{user}/assign-role', [AdminUserController::class, 'assignRole'])->name('api.admin.users.assign-role');

        // Doctor Management
        Route::apiResource('/admin/doctors', AdminDoctorController::class)->names('api.admin.doctors');
        Route::get('/admin/doctors/specialties/list', [AdminDoctorController::class, 'getSpecialties'])->name('api.admin.doctors.specialties');

        // Service Management
        Route::apiResource('/admin/services', AdminServiceController::class)->names('api.admin.services');
        Route::post('/admin/services/{service}/toggle', [AdminServiceController::class, 'toggle'])->name('api.admin.services.toggle');
        Route::patch('/admin/services/{service}/price', [AdminServiceController::class, 'updatePrice'])->name('api.admin.services.price');

        // Medicine Management
        Route::apiResource('/admin/medicines', AdminMedicineController::class)->names('api.admin.medicines');
        Route::patch('/admin/medicines/{medicine}/stock', [AdminMedicineController::class, 'updateStock'])->name('api.admin.medicines.stock');
        Route::get('/admin/medicines/low-stock/list', [AdminMedicineController::class, 'getLowStockMedicines'])->name('api.admin.medicines.low-stock');
        Route::get('/admin/medicines/expiring/list', [AdminMedicineController::class, 'getExpiringMedicines'])->name('api.admin.medicines.expiring');
        Route::get('/admin/medicines/categories/list', [AdminMedicineController::class, 'getCategories'])->name('api.admin.medicines.categories');

        // Inventory Management
        Route::prefix('/admin/inventory')->name('api.admin.inventory.')->group(function (): void {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/import', [InventoryController::class, 'importStock'])->name('import');
            Route::post('/export', [InventoryController::class, 'exportStock'])->name('export');
            Route::get('/value', [InventoryController::class, 'getInventoryValue'])->name('value');
            Route::get('/low-stock', [InventoryController::class, 'getLowStockAlert'])->name('low-stock');
            Route::get('/expiration-alert', [InventoryController::class, 'getExpirationAlert'])->name('expiration-alert');
            Route::get('/report', [InventoryController::class, 'getInventoryReport'])->name('report');
        });

        // Payment Management
        Route::apiResource('/admin/payments', AdminPaymentController::class, ['only' => ['index', 'show']])->names('api.admin.payments');
        Route::post('/admin/payments/{payment}/confirm', [AdminPaymentController::class, 'confirm'])->name('api.admin.payments.confirm');
        Route::post('/admin/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('api.admin.payments.refund');
        Route::get('/admin/payments/stats/summary', [AdminPaymentController::class, 'getStats'])->name('api.admin.payments.stats');
        Route::get('/admin/payments/pending/list', [AdminPaymentController::class, 'getPendingPayments'])->name('api.admin.payments.pending');

        // Appointment Management
        Route::apiResource('/admin/appointments', AdminAppointmentController::class, ['only' => ['index', 'show']])->names('api.admin.appointments');
        Route::post('/admin/appointments/{appointment}/assign-doctor', [AdminAppointmentController::class, 'assignDoctor'])->name('api.admin.appointments.assign-doctor');
        Route::patch('/admin/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('api.admin.appointments.status');
        Route::patch('/admin/appointments/{appointment}/reschedule', [AdminAppointmentController::class, 'reschedule'])->name('api.admin.appointments.reschedule');
        Route::post('/admin/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('api.admin.appointments.cancel');
        Route::get('/admin/appointments/today/list', [AdminAppointmentController::class, 'getTodayAppointments'])->name('api.admin.appointments.today');
        Route::get('/admin/appointments/upcoming/list', [AdminAppointmentController::class, 'getUpcomingAppointments'])->name('api.admin.appointments.upcoming');

        // Pet & Customer Management
        Route::apiResource('/admin/pets', AdminPetController::class, ['only' => ['index', 'show']])->names('api.admin.pets');
        Route::get('/admin/pets/{petId}/owner', [AdminPetController::class, 'getOwnerPets'])->name('api.admin.pets.owner');
        Route::get('/admin/pets/{pet}/appointments', [AdminPetController::class, 'getPetAppointments'])->name('api.admin.pets.appointments');
        Route::get('/admin/pets/{pet}/health-records', [AdminPetController::class, 'getPetHealthRecords'])->name('api.admin.pets.health-records');
        Route::get('/admin/pets/stats/summary', [AdminPetController::class, 'getStats'])->name('api.admin.pets.stats');

        // Reports & Analytics
        Route::prefix('/admin/reports')->name('api.admin.reports.')->group(function (): void {
            Route::get('/appointments', [ReportController::class, 'appointmentStats'])->name('appointments');
            Route::get('/revenue', [ReportController::class, 'revenueReport'])->name('revenue');
            Route::get('/doctor-performance', [ReportController::class, 'doctorPerformance'])->name('doctor-performance');
            Route::get('/service-popularity', [ReportController::class, 'servicePopularity'])->name('service-popularity');
            Route::get('/customers', [ReportController::class, 'customerStats'])->name('customers');
            Route::get('/top-services', [ReportController::class, 'topServices'])->name('top-services');
        });

        // System Settings
        Route::prefix('/admin/settings')->name('api.admin.settings.')->group(function (): void {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::get('/{key}', [SettingController::class, 'show'])->name('show');
            Route::put('/{key}', [SettingController::class, 'update'])->name('update');
            Route::post('/bulk/update', [SettingController::class, 'bulk'])->name('bulk');
            Route::get('/clinic/settings', [SettingController::class, 'getClinicSettings'])->name('clinic.get');
            Route::post('/clinic/settings', [SettingController::class, 'updateClinicSettings'])->name('clinic.update');
        });

        // Activity Logs & Audit Trail
        Route::prefix('/admin/logs')->name('api.admin.logs.')->group(function (): void {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
            Route::get('/user/{userId}', [ActivityLogController::class, 'getUserActivity'])->name('user');
            Route::get('/entity/{entityType}/{entityId}', [ActivityLogController::class, 'getEntityHistory'])->name('entity');
            Route::get('/summary/audit', [ActivityLogController::class, 'getAuditSummary'])->name('summary');
            Route::delete('/clear/old', [ActivityLogController::class, 'clearOldLogs'])->name('clear');
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()?->id,
        'name' => $request->user()?->name,
        'email' => $request->user()?->email,
        'role' => $request->user()?->role?->slug,
    ]);
})->name('api.user');
