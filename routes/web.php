<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('sanctum.auth');
});

Route::redirect('/login', '/sanctum-auth')->name('login');
Route::view('/sanctum-auth', 'auth.sanctum')->name('sanctum.auth');
Route::view('/sanctum-auth/register', 'auth.sanctum-register')->name('sanctum.auth.register');
Route::view('/owner/pets', 'owner.pets')->name('owner.pets');
Route::get('/owner/pets/{pet}/edit', function (int $pet) {
    return view('owner.pet-edit', [
        'petId' => $pet,
    ]);
})->whereNumber('pet')->name('owner.pets.edit');

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
