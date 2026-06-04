<?php

use App\Http\Controllers\VnpayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('sanctum.auth');
});

Route::redirect('/login', '/sanctum-auth')->name('login');
Route::view('/sanctum-auth', 'auth.sanctum')->name('sanctum.auth');
Route::view('/sanctum-auth/register', 'auth.sanctum-register')->name('sanctum.auth.register');
// Owner Routes
Route::middleware(['auth', 'role:owner'])->group(function (): void {
    Route::view('/owner/overview', 'owner.overview')->name('owner.overview');
    Route::view('/owner/profile', 'owner.profile')->name('owner.profile');
    Route::view('/owner/pets', 'owner.pets')->name('owner.pets');
    Route::view('/owner/appointments', 'owner.appointments')->name('owner.appointments');
    Route::view('/owner/shop', 'owner.shop')->name('owner.shop');
    Route::view('/owner/cart', 'owner.cart')->name('owner.cart');
    Route::view('/owner/orders', 'owner.orders')->name('owner.orders');
    Route::view('/owner/ai-diagnosis', 'owner.ai-diagnosis')->name('owner.ai-diagnosis');
    
    Route::get('/owner/shop/medicines/{medicine}', function (int $medicine) {
        return view('owner.medicine-detail', [
            'medicineId' => $medicine,
        ]);
    })->whereNumber('medicine')->name('owner.shop.medicines.detail');
    
    Route::get('/owner/pets/{pet}/edit', function (int $pet) {
        return view('owner.pet-edit', [
            'petId' => $pet,
        ]);
    })->whereNumber('pet')->name('owner.pets.edit');

    Route::get('/owner/pets/{pet}/health-records', function (int $pet) {
        return view('owner.pet-health-records', [
            'petId' => $pet,
        ]);
    })->whereNumber('pet')->name('owner.pets.health-records');

    Route::get('/owner/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Owner dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('owner.dashboard');
});

// Vet Routes
Route::middleware(['auth', 'role:vet'])->group(function (): void {
    Route::view('/vet/appointments', 'vet.appointments')->name('vet.appointments');
    Route::view('/vet/schedule-week', 'vet.appointments-week')->name('vet.schedule-week');
    Route::view('/vet/dashboard', 'vet.workflow', [
        'sectionKey' => 'dashboard',
        'sectionTitle' => 'Dashboard bác sĩ',
        'sectionDescription' => 'Tổng quan ca khám, tái khám, bệnh án đang theo dõi và thông báo khẩn.',
    ])->name('vet.dashboard');
    Route::view('/vet/workflow/schedule', 'vet.workflow', [
        'sectionKey' => 'schedule',
        'sectionTitle' => 'Quản lý lịch khám',
        'sectionDescription' => 'Xem lịch hôm nay, thú cưng chờ khám, nhận ca và quản lý trạng thái ca khám.',
    ])->name('vet.workflow.schedule');
    Route::view('/vet/workflow/intake', 'vet.workflow', [
        'sectionKey' => 'intake',
        'sectionTitle' => 'Khám bệnh - Tiếp nhận ca',
        'sectionDescription' => 'Xem thông tin chủ nuôi, hồ sơ thú cưng, lịch sử tiêm phòng, tiền sử bệnh và tiếp nhận ca.',
    ])->name('vet.workflow.intake');
    Route::view('/vet/workflow/diagnosis', 'vet.workflow', [
        'sectionKey' => 'diagnosis',
        'sectionTitle' => 'Chẩn đoán',
        'sectionDescription' => 'Nhập chẩn đoán sơ bộ, chẩn đoán cuối, bệnh lý và mức độ bệnh.',
    ])->name('vet.workflow.diagnosis');
    Route::view('/vet/workflow/records', 'vet.workflow', [
        'sectionKey' => 'records',
        'sectionTitle' => 'Lập bệnh án',
        'sectionDescription' => 'Tạo cập nhật bệnh án, phác đồ, ghi chú theo dõi và tiến triển bệnh.',
    ])->name('vet.workflow.records');
    Route::view('/vet/workflow/orders', 'vet.workflow', [
        'sectionKey' => 'orders',
        'sectionTitle' => 'Chỉ định dịch vụ xét nghiệm',
        'sectionDescription' => 'Chỉ định xét nghiệm, cận lâm sàng, nhận kết quả và kết luận.',
    ])->name('vet.workflow.orders');
    Route::view('/vet/workflow/prescriptions', 'vet.workflow', [
        'sectionKey' => 'prescriptions',
        'sectionTitle' => 'Kê đơn thuốc',
        'sectionDescription' => 'Chọn thuốc, liều dùng, số ngày dùng và hướng dẫn sử dụng.',
    ])->name('vet.workflow.prescriptions');
    Route::view('/vet/workflow/procedures', 'vet.workflow', [
        'sectionKey' => 'procedures',
        'sectionTitle' => 'Điều trị thủ thuật',
        'sectionDescription' => 'Ghi nhận tiêm thuốc, truyền dịch, tiểu phẫu, phẫu thuật và xử lý cấp cứu.',
    ])->name('vet.workflow.procedures');
    Route::view('/vet/workflow/vaccinations', 'vet.workflow', [
        'sectionKey' => 'vaccinations',
        'sectionTitle' => 'Quản lý tiêm phòng',
        'sectionDescription' => 'Theo dõi mũi tiêm, lịch hẹn mũi sau và phản ứng sau tiêm.',
    ])->name('vet.workflow.vaccinations');
    Route::view('/vet/workflow/inpatient', 'vet.workflow', [
        'sectionKey' => 'inpatient',
        'sectionTitle' => 'Theo dõi nội trú',
        'sectionDescription' => 'Cập nhật tiến triển, dấu hiệu sinh tồn và điều chỉnh phác đồ cho ca nội trú.',
    ])->name('vet.workflow.inpatient');
    Route::view('/vet/workflow/follow-up', 'vet.workflow', [
        'sectionKey' => 'follow_up',
        'sectionTitle' => 'Tái khám',
        'sectionDescription' => 'Hẹn tái khám, đánh giá hồi phục và điều chỉnh điều trị.',
    ])->name('vet.workflow.follow-up');
    Route::view('/vet/workflow/sign-off', 'vet.workflow', [
        'sectionKey' => 'sign_off',
        'sectionTitle' => 'Ký xác nhận chuyên môn',
        'sectionDescription' => 'Duyệt bệnh án, kết luận điều trị, phiếu phẫu thuật và chỉ định thuốc.',
    ])->name('vet.workflow.sign-off');
    
    Route::get('/vet/appointments/{appointment}', function (int $appointment) {
        return view('vet.appointment-detail', [
            'appointmentId' => $appointment,
        ]);
    })->whereNumber('appointment')->name('vet.appointments.show');
    Route::view('/vet/ai-diagnosis', 'vet.ai-diagnosis')->name('vet.ai-diagnosis');
});

// Receptionist Routes
Route::middleware(['auth', 'role:receptionist'])->group(function (): void {
    Route::redirect('/receptionist/appoiintments', '/receptionist/appointments');
    Route::redirect('/receptioniist/appointments', '/receptionist/appointments');
    Route::redirect('/receptioniist/appoiintments', '/receptionist/appointments');
    Route::redirect('/receptionist/walkins', '/receptionist/walk-ins');
    Route::view('/receptionist/dashboard', 'receptionist.dashboard')->name('receptionist.dashboard');
    Route::view('/receptionist/walk-ins', 'receptionist.walkins')->name('receptionist.walkins');
    Route::view('/receptionist/appointments', 'receptionist.appointments')->name('receptionist.appointments');
    Route::view('/receptionist/shop', 'receptionist.shop')->name('receptionist.shop');
    Route::view('/receptionist/billing', 'receptionist.billing')->name('receptionist.billing');
    
    Route::get('/receptionist/appointments/{appointment}', function (int $appointment) {
        return view('receptionist.appointment-detail', [
            'appointmentId' => $appointment,
        ]);
    })->whereNumber('appointment')->name('receptionist.appointments.show');

    Route::get('/receptionist/data', function (Request $request) {
        return response()->json([
            'dashboard' => 'Receptionist dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('receptionist.data');
});

// Public / External API Routes
Route::get('/payments/vnpay/return', [VnpayController::class, 'handleReturn'])->name('payments.vnpay.return');

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    // Admin frontend pages (each view mounts the appropriate Vue page via `data-page`)
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/admin/medicines', 'admin.medicines')->name('admin.medicines');
    Route::view('/admin/users', 'admin.users')->name('admin.users');
    Route::view('/admin/doctors', 'admin.doctors')->name('admin.doctors');
    Route::view('/admin/services', 'admin.services')->name('admin.services');
    Route::view('/admin/pets', 'admin.pets')->name('admin.pets');
    Route::view('/admin/appointments', 'admin.appointments')->name('admin.appointments');
    Route::view('/admin/payments', 'admin.payments')->name('admin.payments');
    Route::view('/admin/inventory', 'admin.inventory')->name('admin.inventory');
    Route::view('/admin/reports', 'admin.reports')->name('admin.reports');
    Route::view('/admin/settings', 'admin.settings')->name('admin.settings');
    Route::view('/admin/logs', 'admin.logs')->name('admin.logs');
});
