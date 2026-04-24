<?php

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

Route::middleware(['auth', 'role:vet'])->group(function (): void {
    Route::redirect('/vet/dashboard', '/vet/appointments')->name('vet.dashboard');
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
    Route::redirect('/admin/dashboard', '/admin/medicines')->name('admin.dashboard');
});
