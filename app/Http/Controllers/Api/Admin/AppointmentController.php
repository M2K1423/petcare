<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Appointment::with(['pet', 'owner', 'doctor', 'service']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('appointment_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('appointment_at', '<=', $request->date_to);
        }

        if ($request->has('date')) {
            $query->whereDate('appointment_at', $request->date);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('owner', fn($oq) => $oq->where('name', 'like', "%$search%"))
                  ->orWhereHas('pet', fn($pq) => $pq->where('name', 'like', "%$search%"));
            });
        }

        $perPage = $request->input('per_page', 20);
        $appointments = $query->orderBy('appointment_at', 'desc')->paginate($perPage);

        return response()->json($appointments);
    }

    public function show(Appointment $appointment)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($appointment->load(['pet', 'owner', 'doctor', 'service', 'medicalRecords']));
    }

    public function assignDoctor(Request $request, Appointment $appointment)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $oldDoctorId = $appointment->doctor_id;
        $appointment->doctor_id = $validated['doctor_id'];
        $appointment->save();

        ActivityLog::log(
            auth()->id(),
            'assign_doctor',
            'Appointment',
            $appointment->id,
            ['doctor_id' => $oldDoctorId],
            ['doctor_id' => $appointment->doctor_id],
            "Assigned doctor to appointment #{$appointment->id}"
        );

        return response()->json(['message' => 'Doctor assigned', 'appointment' => $appointment->load(['doctor'])]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $validated['status'];
        $appointment->save();

        ActivityLog::log(
            auth()->id(),
            'update_status',
            'Appointment',
            $appointment->id,
            ['status' => $oldStatus],
            ['status' => $appointment->status],
            "Updated appointment status: {$oldStatus} → {$appointment->status}"
        );

        return response()->json(['message' => 'Status updated', 'appointment' => $appointment]);
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'appointment_at' => 'required|date|after:now',
        ]);

        $oldDate = $appointment->appointment_at;
        $appointment->appointment_at = $validated['appointment_at'];
        $appointment->save();

        ActivityLog::log(
            auth()->id(),
            'reschedule',
            'Appointment',
            $appointment->id,
            ['appointment_at' => $oldDate],
            ['appointment_at' => $appointment->appointment_at],
            "Rescheduled appointment: {$oldDate} → {$appointment->appointment_at}"
        );

        return response()->json(['message' => 'Appointment rescheduled', 'appointment' => $appointment]);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = 'cancelled';
        $appointment->notes = ($appointment->notes ?? '') . "\n[Cancelled by admin: {$validated['reason']}]";
        $appointment->save();

        ActivityLog::log(
            auth()->id(),
            'cancel',
            'Appointment',
            $appointment->id,
            ['status' => $oldStatus],
            ['status' => 'cancelled'],
            "Cancelled appointment: {$validated['reason']}"
        );

        return response()->json(['message' => 'Appointment cancelled', 'appointment' => $appointment]);
    }

    public function getTodayAppointments()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::whereDate('appointment_at', now())
            ->with(['pet', 'owner', 'doctor', 'service'])
            ->orderBy('appointment_at', 'asc')
            ->get();

        return response()->json([
            'count' => $appointments->count(),
            'appointments' => $appointments,
        ]);
    }

    public function getUpcomingAppointments()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::where('appointment_at', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['pet', 'owner', 'doctor', 'service'])
            ->orderBy('appointment_at', 'asc')
            ->limit(20)
            ->get();

        return response()->json($appointments);
    }
}
