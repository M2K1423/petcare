<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\SanctumAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SanctumAuthController extends Controller
{
    public function __construct(
        private readonly SanctumAuthService $authService,
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        return response()->json($this->authService->register($validated), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        return response()->json($this->authService->login($validated));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->authService->me($request->user()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$request->user()?->id],
        ]);

        return response()->json($this->authService->updateProfile($request->user(), $validated));
    }
}
