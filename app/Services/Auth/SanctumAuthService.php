<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SanctumAuthService
{
    /**
     * @param  array{name:string,email:string,password:string}  $payload
     */
    public function register(array $payload): array
    {
        $ownerRoleId = Role::query()->where('slug', Role::OWNER)->value('id');

        $user = User::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'role_id' => $ownerRoleId,
        ]);

        return $this->createAuthPayload($user, 'Register successful.');
    }

    /**
     * @param  array{email:string,password:string}  $payload
     */
    public function login(array $payload): array
    {
        $user = User::query()->where('email', $payload['email'])->first();

        if (! $user || ! Hash::check($payload['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        return $this->createAuthPayload($user, 'Login successful.');
    }

    public function logout(?User $user): void
    {
        $user?->tokens()->delete();
    }

    public function me(?User $user): array
    {
        return [
            'authenticated' => (bool) $user,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->slug,
            ] : null,
            'redirect_url' => $this->dashboardUrlForRole($user?->role?->slug),
        ];
    }

    /**
     * @param  array{name:string,email:string}  $payload
     */
    public function updateProfile(?User $user, array $payload): array
    {
        if (! $user) {
            throw ValidationException::withMessages([
                'user' => ['Unauthenticated.'],
            ]);
        }

        $user->forceFill([
            'name' => $payload['name'],
            'email' => $payload['email'],
        ])->save();

        return [
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->slug,
            ],
        ];
    }

    public function dashboardUrlForRole(?string $role): ?string
    {
        return match ($role) {
            Role::ADMIN => '/api/admin/dashboard',
            Role::VET => '/api/vet/dashboard',
            Role::RECEPTIONIST => '/api/receptionist/dashboard',
            Role::OWNER => '/api/owner/dashboard',
            default => null,
        };
    }

    private function createAuthPayload(User $user, string $message): array
    {
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'message' => $message,
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->slug,
            ],
            'redirect_url' => $this->dashboardUrlForRole($user->role?->slug),
        ];
    }
}
