<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerAppointmentRequest;
use App\Models\Appointment;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->with(['pet:id,name,species_id', 'pet.species:id,name'])
            ->where('owner_id', $request->user()->id)
            ->latest('appointment_at')
            ->get();

        return response()->json([
            'data' => $appointments,
        ]);
    }

    public function store(StoreOwnerAppointmentRequest $request): JsonResponse
    {
        $pet = Pet::query()->findOrFail((int) $request->input('pet_id'));

        if ((int) $pet->owner_id !== (int) $request->user()->id) {
            abort(403, 'You can only create appointments for your own pets.');
        }

        $appointmentAt = $this->buildAppointmentAt(
            (string) $request->input('appointment_date'),
            (string) $request->input('appointment_time')
        );

        $appointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $request->user()->id,
            'appointment_at' => $appointmentAt,
            'status' => 'pending',
            'reason' => $request->input('reason'),
        ]);

        return response()->json([
            'message' => 'Appointment created successfully.',
            'data' => $appointment->load(['pet:id,name,species_id', 'pet.species:id,name']),
        ], 201);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if ((int) $appointment->owner_id !== (int) $request->user()->id) {
            abort(403, 'You can only cancel your own appointments.');
        }

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Appointment is already cancelled.',
                'data' => $appointment,
            ]);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json([
            'message' => 'Appointment cancelled successfully.',
            'data' => $appointment,
        ]);
    }

    private function buildAppointmentAt(string $date, string $time): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return Carbon::parse($date)->setTime($hour, $minute, 0);
    }
}