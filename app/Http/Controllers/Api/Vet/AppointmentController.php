<?php

namespace App\Http\Controllers\Api\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);

        $query = Appointment::query()
            ->with(['pet.species', 'owner', 'service', 'medicalRecord'])
            ->where('doctor_id', $doctor->id);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_at', $request->string('date')->toString());
        }

        $appointments = $query
            ->orderBy('appointment_at')
            ->get();

        return response()->json([
            'data' => $appointments,
        ]);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot access this appointment.');

        return response()->json([
            'data' => $appointment->load([
                'pet.species',
                'owner',
                'doctor.user',
                'service',
                'medicalRecord',
            ]),
        ]);
    }

    public function saveMedicalRecord(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot update this appointment.');

        $validated = $request->validate([
            'symptoms' => 'nullable|string|max:2000',
            'diagnosis' => 'required|string|max:2000',
            'treatment' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'record_date' => 'nullable|date',
        ]);

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled appointments cannot be examined.',
            ], 422);
        }

        $appointment = DB::transaction(function () use ($appointment, $doctor, $validated) {
            MedicalRecord::query()->updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'pet_id' => $appointment->pet_id,
                    'doctor_id' => $doctor->id,
                    'symptoms' => $validated['symptoms'] ?? null,
                    'diagnosis' => $validated['diagnosis'],
                    'treatment' => $validated['treatment'],
                    'notes' => $validated['notes'] ?? null,
                    'record_date' => $validated['record_date'] ?? Carbon::today()->toDateString(),
                ],
            );

            $appointment->update([
                'status' => 'completed',
            ]);

            return $appointment->fresh([
                'pet.species',
                'owner',
                'doctor.user',
                'service',
                'medicalRecord',
            ]);
        });

        return response()->json([
            'message' => 'Medical record saved successfully.',
            'data' => $appointment,
        ]);
    }

    private function resolveDoctor(Request $request): Doctor
    {
        return Doctor::query()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
