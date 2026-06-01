<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {
    }

    /**
     * Danh sach lich hen (co filter)
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['pet', 'owner', 'doctor.user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_at', $request->date);
        }

        $appointments = $query->orderBy('appointment_at', 'asc')->paginate(15);

        return response()->json($appointments);
    }

    /**
     * Chi tiet lich hen
     */
    public function show($id)
    {
        $appointment = Appointment::with(['pet.species', 'owner', 'doctor.user', 'service'])
            ->findOrFail($id);

        return response()->json([
            'data' => $appointment,
        ]);
    }

    /**
     * Tao lich hen
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'owner_id' => 'required|exists:users,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_at' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string',
            'is_emergency' => 'nullable|boolean',
        ]);

        $validated['status'] = 'confirmed';
        $validated['is_emergency'] = $request->boolean('is_emergency');

        $appointment = Appointment::create($validated);

        $this->assignQueueNumber($appointment);

        if (! empty($validated['doctor_id'])) {
            $this->notifyAssignedDoctor($appointment->fresh(['pet', 'doctor.user']));
        }

        return response()->json([
            'message' => 'Tao lich hen thanh cong.',
            'data' => $appointment->load(['pet', 'owner', 'doctor.user', 'service']),
        ], 201);
    }

    /**
     * Check-in khi khach den
     */
    public function checkIn($id)
    {
        $appointment = Appointment::findOrFail($id);

        if (in_array($appointment->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Lich hen da hoan thanh hoac da bi huy.',
            ], 400);
        }

        $appointment->update(['status' => 'confirmed']);

        $this->assignQueueNumber($appointment);

        return response()->json([
            'message' => 'Check-in thanh cong. Thu cung da vao hang doi.',
            'data' => $appointment,
        ]);
    }

    /**
     * Danh sach hang doi hom nay
     */
    public function queue()
    {
        $queue = Appointment::with(['pet', 'owner', 'doctor.user'])
            ->whereDate('appointment_at', Carbon::today())
            ->where('status', 'confirmed')
            ->orderByDesc('is_emergency')
            ->orderBy('queue_number')
            ->get();

        return response()->json([
            'message' => 'Danh sach hang doi',
            'data' => $queue,
        ]);
    }

    /**
     * Danh dau ca cap cuu
     */
    public function markEmergency($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->is_emergency = true;

        if ($appointment->status === 'pending') {
            $appointment->status = 'confirmed';
            $this->assignQueueNumber($appointment);
        }

        $appointment->save();

        return response()->json([
            'message' => 'Da danh dau ca cap cuu.',
            'data' => $appointment,
        ]);
    }

    /**
     * Gan so thu tu hang doi
     */
    private function assignQueueNumber(Appointment $appointment): void
    {
        if ($appointment->queue_number) {
            return;
        }

        $date = Carbon::parse($appointment->appointment_at)->toDateString();

        $maxQueue = Appointment::whereDate('appointment_at', $date)
            ->max('queue_number') ?? 0;

        $appointment->update([
            'queue_number' => $maxQueue + 1,
        ]);
    }

    private function notifyAssignedDoctor(Appointment $appointment): void
    {
        $doctor = $appointment->doctor;

        if (! $doctor?->user_id) {
            return;
        }

        $this->notifications->create([
            'user_id' => $doctor->user_id,
            'appointment_id' => $appointment->id,
            'type' => 'appointment_assigned',
            'title' => 'Có lịch hẹn mới được phân công',
            'message' => "Bạn vừa được phân công lịch hẹn cho {$appointment->pet?->name} vào " . $appointment->appointment_at?->format('H:i d/m/Y') . '.',
        ]);
    }

    /**
     * Phan cong bac si cho lich hen
     */
    public function assignDoctor(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $appointment->doctor_id = $validated['doctor_id'];
        $appointment->save();

        $appointment->load(['doctor.user', 'pet']);

        if ($appointment->doctor?->user_id) {
            $this->notifications->create([
                'user_id' => $appointment->doctor->user_id,
                'appointment_id' => $appointment->id,
                'type' => 'appointment_assigned',
                'title' => 'Có lịch hẹn mới được phân công',
                'message' => "Bạn vừa được phân công lịch hẹn cho {$appointment->pet?->name} vào " . $appointment->appointment_at?->format('H:i d/m/Y') . '.',
            ]);
        }

        return response()->json([
            'message' => 'Phân công bác sĩ thành công.',
            'data' => $appointment->load(['doctor.user', 'pet', 'owner', 'service']),
        ]);
    }
}
