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
use App\Http\Controllers\Api\Admin\MedicineController as AdminMedicineController;
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
        Route::get('/admin/dashboard', function (Request $request) {
            return response()->json([
                'dashboard' => 'Admin dashboard',
                'user' => $request->user()?->only(['id', 'name', 'email']),
                'role' => Role::ADMIN,
            ]);
        })->name('api.admin.dashboard');

        Route::get('/admin/medicines', [AdminMedicineController::class, 'index'])->name('api.admin.medicines.index');
        Route::post('/admin/medicines', [AdminMedicineController::class, 'store'])->name('api.admin.medicines.store');
        Route::put('/admin/medicines/{medicine}', [AdminMedicineController::class, 'update'])->name('api.admin.medicines.update');
        Route::delete('/admin/medicines/{medicine}', [AdminMedicineController::class, 'destroy'])->name('api.admin.medicines.destroy');
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
