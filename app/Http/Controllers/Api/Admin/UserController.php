<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('role');

        if ($request->has('role')) {
            $query->whereHas('role', fn($q) => $q->where('slug', $request->role));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->password = Hash::make($validated['password']);
        $user->role_id = $validated['role_id'];
        $user->save();

        ActivityLog::log(
            auth()->id(),
            'create',
            'User',
            $user->id,
            [],
            $user->only(['name', 'email', 'phone', 'role_id']),
            "Created user: {$user->name}"
        );

        return response()->json($user->load('role'), Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user->load('role', 'activityLogs'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'sometimes|exists:roles,id',
        ]);

        $oldValues = $user->only(['name', 'email', 'phone', 'role_id']);
        $user->update($validated);
        $newValues = $user->only(['name', 'email', 'phone', 'role_id']);

        ActivityLog::log(
            auth()->id(),
            'update',
            'User',
            $user->id,
            $oldValues,
            $newValues,
            "Updated user: {$user->name}"
        );

        return response()->json($user->load('role'));
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $userId = $user->id;
        $userName = $user->name;

        $user->delete();

        ActivityLog::log(
            auth()->id(),
            'delete',
            'User',
            $userId,
            $user->only(['name', 'email', 'role_id']),
            [],
            "Deleted user: {$userName}"
        );

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function lock(User $user)
    {
        $this->authorize('lock', $user);

        $user->is_locked = true;
        $user->locked_at = now();
        $user->save();

        ActivityLog::log(
            auth()->id(),
            'lock',
            'User',
            $user->id,
            ['is_locked' => false],
            ['is_locked' => true, 'locked_at' => $user->locked_at],
            "Locked user: {$user->name}"
        );

        return response()->json(['message' => 'User locked successfully', 'user' => $user]);
    }

    public function unlock(User $user)
    {
        $this->authorize('unlock', $user);

        $user->is_locked = false;
        $user->locked_at = null;
        $user->save();

        ActivityLog::log(
            auth()->id(),
            'unlock',
            'User',
            $user->id,
            ['is_locked' => true],
            ['is_locked' => false],
            "Unlocked user: {$user->name}"
        );

        return response()->json(['message' => 'User unlocked successfully', 'user' => $user]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('resetPassword', $user);

        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        ActivityLog::log(
            auth()->id(),
            'reset_password',
            'User',
            $user->id,
            [],
            ['password_reset' => true],
            "Reset password for user: {$user->name}"
        );

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function assignRole(Request $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $oldRole = $user->role_id;
        $user->role_id = $validated['role_id'];
        $user->save();

        $oldRoleName = Role::find($oldRole)?->name;
        $newRoleName = Role::find($validated['role_id'])?->name;

        ActivityLog::log(
            auth()->id(),
            'assign_role',
            'User',
            $user->id,
            ['role_id' => $oldRole, 'role_name' => $oldRoleName],
            ['role_id' => $user->role_id, 'role_name' => $newRoleName],
            "Assigned role to user {$user->name}: {$oldRoleName} → {$newRoleName}"
        );

        return response()->json(['message' => 'Role assigned successfully', 'user' => $user->load('role')]);
    }
}
