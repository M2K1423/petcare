<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Tìm kiếm hệ thống hồ sơ Khách hàng và thú cưng (Khách cũ)
     */
    public function search(Request $request)
    {
        $term = $request->query('q');
        
        if (!$term) {
            return response()->json([
                'message' => 'Vui lòng cung cấp tham số tìm kiếm (SĐT hoặc tên).'
            ], 400);
        }

        // Ưu tiên tìm trong Owner
        $owners = User::whereHas('role', fn($q) => $q->where('slug', 'owner'))
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('phone', 'like', "%{$term}%");
            })
            ->with(['pets.medicalRecords']) // Load kèm pet và lịch sử khám
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Kết quả tìm kiếm',
            'data' => $owners
        ]);
    }

    /**
     * Tạo nhanh hồ sơ Khách hàng (Owner) và Thú cưng (Pet) cho khách vãng lai.
     */
    public function storeWalkIn(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'nullable|email',
            'owner_phone' => 'required|string|max:20',
            'pet_name' => 'required|string|max:255',
            'species_id' => 'required|exists:species,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,unknown',
            'weight' => 'nullable|numeric|min:0',
            'condition_option' => 'nullable|string|max:255',
            'condition_custom' => 'nullable|string|max:1000',
            'is_emergency' => 'nullable|boolean',
            'reason' => 'nullable|string|max:1000',
        ]);

        $ownerRole = Role::where('slug', 'owner')->first();

        // Using DB transaction to ensure owner/pet/appointment are created atomically.
        $result = DB::transaction(function () use ($validated, $ownerRole) {
            $normalizedPhone = preg_replace('/\D+/', '', $validated['owner_phone']) ?: $validated['owner_phone'];

            $owner = User::whereHas('role', fn($q) => $q->where('slug', 'owner'))
                ->where('phone', $normalizedPhone)
                ->first();

            // 1. Tạo tài khoản Owner (với mật khẩu mặc định là số điện thoại hoặc chuỗi ngẫu nhiên)
            if (!$owner) {
                $emailToUse = $validated['owner_email'] ?? null;
                if ($emailToUse && User::where('email', $emailToUse)->exists()) {
                    $emailToUse = null;
                }

                $fallbackEmail = 'walkin.' . $normalizedPhone . '.' . now()->timestamp . '@petcare.local';

                $owner = User::create([
                    'name' => $validated['owner_name'],
                    'email' => $emailToUse ?? $fallbackEmail,
                    'phone' => $normalizedPhone,
                    'role_id' => $ownerRole->id,
                    'password' => Hash::make('12345678'),
                ]);
            } else {
                $owner->update([
                    'name' => $validated['owner_name'],
                    'email' => $validated['owner_email'] ?? $owner->email,
                ]);
            }

            // 2. Tạo hồ sơ thú cưng
            $pet = Pet::create([
                'owner_id' => $owner->id,
                'species_id' => $validated['species_id'],
                'name' => $validated['pet_name'],
                'breed' => $validated['breed'] ?? null,
                'gender' => $validated['gender'] ?? 'unknown',
                'weight' => $validated['weight'] ?? null,
            ]);

            $appointmentAt = Carbon::now();
            $maxQueue = Appointment::whereDate('appointment_at', $appointmentAt->toDateString())
                ->max('queue_number') ?? 0;

            $appointment = Appointment::create([
                'pet_id' => $pet->id,
                'owner_id' => $owner->id,
                'doctor_id' => $validated['doctor_id'] ?? null,
                'appointment_at' => $appointmentAt,
                'status' => 'confirmed',
                'queue_number' => $maxQueue + 1,
                'is_emergency' => !empty($validated['is_emergency']),
                'reason' => $validated['reason'] ?? 'Walk-in at reception desk',
            ]);

            return [
                'owner' => $owner,
                'pet' => $pet,
                'appointment' => $appointment,
            ];
        });

        return response()->json([
            'message' => 'Tạo hồ sơ khách vãng lai và đưa vào hàng đợi thành công.',
            'data' => $result
        ], 201);
    }
}
