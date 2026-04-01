<?php

use App\Http\Controllers\Auth\FirebaseSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('firebase.auth');
});

Route::view('/firebase-auth', 'auth.firebase')->name('firebase.auth');

Route::post('/auth/firebase/session-login', [FirebaseSessionController::class, 'store'])
    ->name('firebase.session.login');
Route::post('/auth/logout', [FirebaseSessionController::class, 'destroy'])
    ->name('auth.logout');
Route::get('/auth/me', [FirebaseSessionController::class, 'me'])
    ->name('auth.me');

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
    Route::get('/vet/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Vet dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('vet.dashboard');
});

Route::middleware(['auth', 'role:receptionist'])->group(function (): void {
    Route::get('/receptionist/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Receptionist dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('receptionist.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/admin/dashboard', function (Request $request) {
        return response()->json([
            'dashboard' => 'Admin dashboard',
            'user' => $request->user()?->only(['id', 'name', 'email']),
            'role' => $request->user()?->role?->slug,
        ]);
    })->name('admin.dashboard');
});
