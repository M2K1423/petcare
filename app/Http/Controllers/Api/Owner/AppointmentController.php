<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerAppointmentRequest;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::query()
            ->with(['pet:id,name,species_id', 'pet.species:id,name', 'service:id,name,price'])
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
            abort(403, 'Bạn chỉ có thể tạo lịch hẹn cho thú cưng của mình.');
        }

        $appointmentAt = $this->buildAppointmentAt(
            (string) $request->input('appointment_date'),
            (string) $request->input('appointment_time')
        );

        $appointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $request->user()->id,
            'service_id' => (int) $request->input('service_id'),
            'appointment_at' => $appointmentAt,
            'status' => 'pending',
            'reason' => $request->input('reason'),
        ]);

        $receptionists = User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', Role::RECEPTIONIST))
            ->get(['id']);

        foreach ($receptionists as $receptionist) {
            $this->notifications->create([
                'user_id' => $receptionist->id,
                'appointment_id' => $appointment->id,
                'type' => 'appointment_requested',
                'title' => 'Lịch hẹn mới cần xác nhận',
                'message' => "{$request->user()->name} vừa đặt lịch cho {$pet->name} vào {$appointmentAt->format('H:i d/m/Y')}.",
            ]);
        }

        return response()->json([
            'message' => 'Đã tạo lịch hẹn thành công.',
            'data' => $appointment->load(['pet:id,name,species_id', 'pet.species:id,name', 'service:id,name,price']),
        ], 201);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if ((int) $appointment->owner_id !== (int) $request->user()->id) {
            abort(403, 'Bạn chỉ có thể hủy lịch hẹn của mình.');
        }

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Lịch hẹn đã bị hủy trước đó.',
                'data' => $appointment,
            ]);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json([
            'message' => 'Đã hủy lịch hẹn thành công.',
            'data' => $appointment,
        ]);
    }

    private function buildAppointmentAt(string $date, string $time): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return Carbon::parse($date)->setTime($hour, $minute, 0);
    }
}
