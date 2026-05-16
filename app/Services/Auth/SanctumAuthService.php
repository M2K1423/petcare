<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

        Auth::guard('web')->login($user);

        return $this->createAuthPayload($user, 'Đăng ký thành công.');
    }

    /**
     * @param  array{email:string,password:string}  $payload
     */
    public function login(array $payload): array
    {
        $user = User::query()->where('email', $payload['email'])->first();

        if (! $user || ! Hash::check($payload['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không đúng.'],
            ]);
        }

        Auth::guard('web')->login($user);

        return $this->createAuthPayload($user, 'Đăng nhập thành công.');
    }

    public function logout(?User $user): void
    {
        $token = $user?->currentAccessToken();

        if ($token) {
            \Laravel\Sanctum\PersonalAccessToken::query()->where('id', $token->id)->delete();
        }

        Auth::guard('web')->logout();
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
                'user' => ['Chưa xác thực.'],
            ]);
        }

        $user->forceFill([
            'name' => $payload['name'],
            'email' => $payload['email'],
        ])->save();

        return [
            'message' => 'Đã cập nhật hồ sơ thành công.',
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
            Role::ADMIN => '/admin/medicines',
            Role::VET => '/vet/dashboard',
            Role::RECEPTIONIST => '/receptionist/dashboard',
            Role::OWNER => '/owner/overview',
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
