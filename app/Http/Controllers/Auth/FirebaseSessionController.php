<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FirebaseSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idToken' => ['required', 'string'],
        ]);

        $apiKey = (string) (config('services.firebase.api_key') ?: env('VITE_FIREBASE_API_KEY', ''));

        if ($apiKey === '') {
            return response()->json([
                'message' => 'Missing Firebase API key on server.',
            ], 500);
        }

        $response = Http::asJson()->post(
            "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}",
            ['idToken' => $validated['idToken']]
        );

        if ($response->failed()) {
            return response()->json([
                'message' => 'Firebase token verification failed.',
                'firebase_response' => $response->json(),
            ], 422);
        }

        $firebaseUser = data_get($response->json(), 'users.0');
        $email = (string) data_get($firebaseUser, 'email', '');

        if ($email === '') {
            return response()->json([
                'message' => 'Verified Firebase token does not contain email.',
            ], 422);
        }

        $displayName = (string) data_get($firebaseUser, 'displayName', '');
        $resolvedName = $displayName !== '' ? $displayName : Str::before($email, '@');
        $ownerRoleId = Role::query()->where('slug', Role::OWNER)->value('id');

        $user = User::query()->firstOrNew(['email' => $email]);

        if (! $user->exists) {
            $user->name = $resolvedName;
            $user->password = Str::random(40);
            $user->role_id = $ownerRoleId;
            $user->save();
        } else {
            if ($user->name === '' || $user->name === null) {
                $user->name = $resolvedName;
            }

            if (! $user->role_id) {
                $user->role_id = $ownerRoleId;
            }

            $user->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $roleSlug = $user->role?->slug ?? Role::OWNER;
        $dashboardRouteName = match ($roleSlug) {
            Role::ADMIN => 'admin.dashboard',
            Role::VET => 'vet.dashboard',
            Role::RECEPTIONIST => 'receptionist.dashboard',
            default => 'owner.dashboard',
        };

        return response()->json([
            'message' => 'Laravel session created from Firebase token.',
            'role' => $roleSlug,
            'redirect_url' => route($dashboardRouteName),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Laravel session logged out.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'authenticated' => false,
            ]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->slug,
            ],
        ]);
    }
}
