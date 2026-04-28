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
Route::view('/owner/overview', 'owner.overview')->name('owner.overview');
Route::view('/owner/profile', 'owner.profile')->name('owner.profile');
Route::view('/owner/pets', 'owner.pets')->name('owner.pets');
Route::view('/owner/appointments', 'owner.appointments')->name('owner.appointments');
Route::view('/owner/shop', 'owner.shop')->name('owner.shop');
Route::view('/vet/appointments', 'vet.appointments')->name('vet.appointments');
Route::view('/vet/schedule-week', 'vet.appointments-week')->name('vet.schedule-week');
Route::view('/vet/dashboard', 'vet.workflow', [
    'sectionKey' => 'dashboard',
    'sectionTitle' => 'Dashboard Bac si',
    'sectionDescription' => 'Tong quan ca kham, tai kham, benh an dang theo doi va thong bao khan.',
])->name('vet.dashboard');
Route::view('/vet/workflow/schedule', 'vet.workflow', [
    'sectionKey' => 'schedule',
    'sectionTitle' => 'Quan ly lich kham',
    'sectionDescription' => 'Xem lich hom nay, thu cung cho kham, nhan ca va quan ly trang thai ca kham.',
])->name('vet.workflow.schedule');
Route::view('/vet/workflow/intake', 'vet.workflow', [
    'sectionKey' => 'intake',
    'sectionTitle' => 'Kham benh - Tiep nhan ca',
    'sectionDescription' => 'Xem thong tin chu nuoi, pet profile, lich su tiem phong, tien su benh va tiep nhan ca.',
])->name('vet.workflow.intake');
Route::view('/vet/workflow/diagnosis', 'vet.workflow', [
    'sectionKey' => 'diagnosis',
    'sectionTitle' => 'Chan doan',
    'sectionDescription' => 'Nhap chan doan so bo, chan doan cuoi, benh ly va muc do benh.',
])->name('vet.workflow.diagnosis');
Route::view('/vet/workflow/records', 'vet.workflow', [
    'sectionKey' => 'records',
    'sectionTitle' => 'Lap benh an',
    'sectionDescription' => 'Tao cap nhat benh an, phac do, ghi chu theo doi va tien trien benh.',
])->name('vet.workflow.records');
Route::view('/vet/workflow/orders', 'vet.workflow', [
    'sectionKey' => 'orders',
    'sectionTitle' => 'Chi dinh dich vu xet nghiem',
    'sectionDescription' => 'Chi dinh xet nghiem, can lam sang, nhan ket qua va ket luan.',
])->name('vet.workflow.orders');
Route::view('/vet/workflow/prescriptions', 'vet.workflow', [
    'sectionKey' => 'prescriptions',
    'sectionTitle' => 'Ke don thuoc',
    'sectionDescription' => 'Chon thuoc, lieu dung, so ngay dung va huong dan su dung.',
])->name('vet.workflow.prescriptions');
Route::view('/vet/workflow/procedures', 'vet.workflow', [
    'sectionKey' => 'procedures',
    'sectionTitle' => 'Dieu tri thu thuat',
    'sectionDescription' => 'Ghi nhan tiem thuoc, truyen dich, tieu phau, phau thuat va xu ly cap cuu.',
])->name('vet.workflow.procedures');
Route::view('/vet/workflow/vaccinations', 'vet.workflow', [
    'sectionKey' => 'vaccinations',
    'sectionTitle' => 'Quan ly tiem phong',
    'sectionDescription' => 'Theo doi mui tiem, lich hen mui sau va phan ung sau tiem.',
])->name('vet.workflow.vaccinations');
Route::view('/vet/workflow/inpatient', 'vet.workflow', [
    'sectionKey' => 'inpatient',
    'sectionTitle' => 'Theo doi noi tru',
    'sectionDescription' => 'Cap nhat tien trien, dau hieu sinh ton va dieu chinh phac do cho ca noi tru.',
])->name('vet.workflow.inpatient');
Route::view('/vet/workflow/follow-up', 'vet.workflow', [
    'sectionKey' => 'follow_up',
    'sectionTitle' => 'Tai kham',
    'sectionDescription' => 'Hen tai kham, danh gia hoi phuc va dieu chinh dieu tri.',
])->name('vet.workflow.follow-up');
Route::view('/vet/workflow/sign-off', 'vet.workflow', [
    'sectionKey' => 'sign_off',
    'sectionTitle' => 'Ky xac nhan chuyen mon',
    'sectionDescription' => 'Duyet benh an, ket luan dieu tri, phieu phau thuat va chi dinh thuoc.',
])->name('vet.workflow.sign-off');
Route::get('/vet/appointments/{appointment}', function (int $appointment) {
    return view('vet.appointment-detail', [
        'appointmentId' => $appointment,
    ]);
})->whereNumber('appointment')->name('vet.appointments.show');
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

Route::middleware(['auth', 'role:owner'])->group(function (): void {
    Route::get('/owner/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Owner dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('owner.dashboard');
});

// Receptionist Frontend Routes
Route::redirect('/receptionist/appoiintments', '/receptionist/appointments');
Route::redirect('/receptioniist/appointments', '/receptionist/appointments');
Route::redirect('/receptioniist/appoiintments', '/receptionist/appointments');
Route::view('/receptionist/dashboard', 'receptionist.dashboard')->name('receptionist.dashboard');
Route::view('/receptionist/walk-ins', 'receptionist.walkins')->name('receptionist.walkins');
Route::view('/receptionist/appointments', 'receptionist.appointments')->name('receptionist.appointments');
Route::get('/receptionist/appointments/{appointment}', function (int $appointment) {
    return view('receptionist.appointment-detail', [
        'appointmentId' => $appointment,
    ]);
})->whereNumber('appointment')->name('receptionist.appointments.show');
Route::view('/receptionist/billing', 'receptionist.billing')->name('receptionist.billing');
Route::get('/payments/vnpay/return', [VnpayController::class, 'handleReturn'])->name('payments.vnpay.return');
Route::view('/admin/medicines', 'admin.medicines')->name('admin.medicines');

Route::middleware(['auth', 'role:receptionist'])->group(function (): void {
    Route::get('/receptionist/data', function (Request $request) {
        return response()->json([
            'dashboard' => 'Receptionist dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('receptionist.dashboard');
});

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
