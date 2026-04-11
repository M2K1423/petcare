<?php

use App\Http\Controllers\Api\Owner\AppointmentController;
use App\Http\Controllers\Api\Owner\PetController;
use App\Http\Controllers\Api\Owner\SpeciesController;
use App\Http\Controllers\Api\Auth\SanctumAuthController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [SanctumAuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [SanctumAuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [SanctumAuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [SanctumAuthController::class, 'me'])->name('api.auth.me');
    Route::put('/auth/profile', [SanctumAuthController::class, 'updateProfile'])->name('api.auth.profile.update');

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
        Route::get('/owner/appointments', [AppointmentController::class, 'index'])->name('api.owner.appointments.index');
        Route::post('/owner/appointments', [AppointmentController::class, 'store'])->name('api.owner.appointments.store');
        Route::delete('/owner/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('api.owner.appointments.destroy');
    });

    Route::middleware('role:vet')->get('/vet/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Vet dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => Role::VET,
        ]);
    })->name('api.vet.dashboard');

    Route::middleware('role:receptionist')->get('/receptionist/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Receptionist dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => Role::RECEPTIONIST,
        ]);
    })->name('api.receptionist.dashboard');

    Route::middleware('role:admin')->get('/admin/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Admin dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => Role::ADMIN,
        ]);
    })->name('api.admin.dashboard');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()?->id,
        'name' => $request->user()?->name,
        'email' => $request->user()?->email,
        'role' => $request->user()?->role?->slug,
    ]);
})->name('api.user');

