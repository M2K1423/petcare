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
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function workflowStatuses(): array
    {
        return [
            Appointment::WORKFLOW_AWAITING_EXAM,
            Appointment::WORKFLOW_EXAMINING,
            Appointment::WORKFLOW_AWAITING_LAB,
            Appointment::WORKFLOW_TREATING,
            Appointment::WORKFLOW_COMPLETED,
            Appointment::WORKFLOW_FOLLOW_UP,
        ];
    }

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

        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->string('workflow_status')->toString());
        }

        $appointments = $query
            ->orderBy('appointment_at')
            ->get();

        $today = Carbon::today()->toDateString();

        return response()->json([
            'data' => $appointments,
            'meta' => [
                'today_total' => $appointments->filter(
                    fn (Appointment $item): bool => ($item->appointment_at?->toDateString() ?? '') === $today
                )->count(),
                'workflow_counts' => [
                    Appointment::WORKFLOW_AWAITING_EXAM => $appointments->where('workflow_status', Appointment::WORKFLOW_AWAITING_EXAM)->count(),
                    Appointment::WORKFLOW_EXAMINING => $appointments->where('workflow_status', Appointment::WORKFLOW_EXAMINING)->count(),
                    Appointment::WORKFLOW_AWAITING_LAB => $appointments->where('workflow_status', Appointment::WORKFLOW_AWAITING_LAB)->count(),
                    Appointment::WORKFLOW_TREATING => $appointments->where('workflow_status', Appointment::WORKFLOW_TREATING)->count(),
                    Appointment::WORKFLOW_COMPLETED => $appointments->where('workflow_status', Appointment::WORKFLOW_COMPLETED)->count(),
                    Appointment::WORKFLOW_FOLLOW_UP => $appointments->where('workflow_status', Appointment::WORKFLOW_FOLLOW_UP)->count(),
                ],
            ],
        ]);
    }

    public function dashboardSummary(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        $today = Carbon::today();

        $todayAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_at', $today->toDateString())
            ->get();

        $followUpAppointments = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '>=', $today->toDateString())
            ->count();

        $monitoringRecords = MedicalRecord::query()
            ->where('doctor_id', $doctor->id)
            ->whereNotNull('disease_progress')
            ->whereDate('updated_at', '>=', $today->copy()->subDays(14)->toDateString())
            ->count();

        $inpatientCases = MedicalRecord::query()
            ->where('doctor_id', $doctor->id)
            ->whereNotNull('progress_logs')
            ->whereDate('updated_at', '>=', $today->copy()->subDays(2)->toDateString())
            ->count();

        return response()->json([
            'data' => [
                'today_total' => $todayAppointments->count(),
                'today_waiting_exam' => $todayAppointments->where('workflow_status', Appointment::WORKFLOW_AWAITING_EXAM)->count(),
                'today_examining' => $todayAppointments->where('workflow_status', Appointment::WORKFLOW_EXAMINING)->count(),
                'today_awaiting_lab' => $todayAppointments->where('workflow_status', Appointment::WORKFLOW_AWAITING_LAB)->count(),
                'today_treating' => $todayAppointments->where('workflow_status', Appointment::WORKFLOW_TREATING)->count(),
                'today_completed' => $todayAppointments->where('workflow_status', Appointment::WORKFLOW_COMPLETED)->count(),
                'follow_up_upcoming' => $followUpAppointments,
                'monitoring_records' => $monitoringRecords,
                'inpatient_cases' => $inpatientCases,
            ],
        ]);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot access this appointment.');

        $appointment->load([
            'pet.species',
            'pet.vaccinations',
            'owner',
            'doctor.user',
            'service',
            'medicalRecord',
        ]);

        $previousMedicalRecords = MedicalRecord::query()
            ->where('pet_id', $appointment->pet_id)
            ->where('id', '!=', $appointment->medicalRecord?->id ?? 0)
            ->latest('record_date')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => $appointment,
            'meta' => [
                'vaccination_history' => $appointment->pet?->vaccinations ?? [],
                'previous_medical_records' => $previousMedicalRecords,
            ],
        ]);
    }

    public function acceptCase(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot accept this appointment.');

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled appointments cannot be accepted.',
            ], 422);
        }

        $appointment->forceFill([
            'workflow_status' => Appointment::WORKFLOW_EXAMINING,
            'status' => 'confirmed',
            'accepted_at' => $appointment->accepted_at ?? Carbon::now(),
            'started_at' => $appointment->started_at ?? Carbon::now(),
        ])->save();

        return response()->json([
            'message' => 'Case accepted successfully.',
            'data' => $appointment->fresh(['pet.species', 'owner', 'service', 'medicalRecord']),
        ]);
    }

    public function updateWorkflowStatus(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot update this appointment.');

        $validated = $request->validate([
            'workflow_status' => ['required', 'string', 'in:' . implode(',', $this->workflowStatuses())],
            'follow_up_at' => 'nullable|date',
        ]);

        $workflowStatus = $validated['workflow_status'];

        $payload = [
            'workflow_status' => $workflowStatus,
        ];

        if ($workflowStatus === Appointment::WORKFLOW_EXAMINING && ! $appointment->started_at) {
            $payload['started_at'] = Carbon::now();
            $payload['status'] = 'confirmed';
        }

        if ($workflowStatus === Appointment::WORKFLOW_COMPLETED) {
            $payload['status'] = 'completed';
            $payload['completed_at'] = Carbon::now();
        }

        if ($workflowStatus === Appointment::WORKFLOW_FOLLOW_UP) {
            $payload['status'] = 'confirmed';
            $payload['follow_up_at'] = $validated['follow_up_at'] ?? $appointment->follow_up_at;
        }

        if (in_array($workflowStatus, [Appointment::WORKFLOW_AWAITING_EXAM, Appointment::WORKFLOW_AWAITING_LAB, Appointment::WORKFLOW_TREATING], true)) {
            $payload['status'] = 'confirmed';
        }

        $appointment->forceFill($payload)->save();

        return response()->json([
            'message' => 'Workflow status updated successfully.',
            'data' => $appointment->fresh(['pet.species', 'owner', 'service', 'medicalRecord']),
        ]);
    }

    public function saveMedicalRecord(Request $request, Appointment $appointment): JsonResponse
    {
        $doctor = $this->resolveDoctor($request);
        abort_if($appointment->doctor_id !== $doctor->id, 403, 'You cannot update this appointment.');

        $validated = $request->validate([
            'temperature_c' => 'nullable|numeric|min:30|max:45',
            'weight_kg' => 'nullable|numeric|min:0|max:300',
            'heart_rate_bpm' => 'nullable|integer|min:1|max:400',
            'symptoms' => 'nullable|string|max:2000',
            'abnormal_signs' => 'nullable|string|max:2000',
            'preliminary_diagnosis' => 'nullable|string|max:2000',
            'diagnosis' => 'required|string|max:2000',
            'final_diagnosis' => 'nullable|string|max:2000',
            'pathology' => 'nullable|string|max:255',
            'severity_level' => 'nullable|in:mild,moderate,severe,critical',
            'prescription' => 'nullable|string|max:2000|required_without:treatment',
            'treatment' => 'nullable|string|max:2000|required_without:prescription',
            'treatment_protocol' => 'nullable|string|max:4000',
            'disease_progress' => 'nullable|string|max:4000',
            'follow_up_plan' => 'nullable|string|max:2000',
            'service_orders' => 'nullable|array|max:20',
            'service_orders.*.name' => 'required_with:service_orders|string|max:255',
            'service_orders.*.status' => 'nullable|string|max:50',
            'service_orders.*.result' => 'nullable|string|max:2000',
            'prescriptions' => 'nullable|array|max:30',
            'prescriptions.*.medicine_name' => 'required_with:prescriptions|string|max:255',
            'prescriptions.*.dosage' => 'nullable|string|max:255',
            'prescriptions.*.days' => 'nullable|integer|min:1|max:365',
            'prescriptions.*.instructions' => 'nullable|string|max:1000',
            'procedures' => 'nullable|array|max:20',
            'procedures.*.name' => 'required_with:procedures|string|max:255',
            'procedures.*.status' => 'nullable|string|max:50',
            'procedures.*.notes' => 'nullable|string|max:2000',
            'progress_logs' => 'nullable|array|max:100',
            'progress_logs.*.noted_at' => 'nullable|date',
            'progress_logs.*.note' => 'required_with:progress_logs|string|max:2000',
            'progress_logs.*.vitals' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'record_date' => 'nullable|date',
            'workflow_status' => ['nullable', 'string', 'in:' . implode(',', $this->workflowStatuses())],
            'follow_up_at' => 'nullable|date',
            'sign_off' => 'nullable|boolean',
        ]);

        $prescription = $validated['prescription'] ?? $validated['treatment'] ?? null;

        if ($appointment->status === 'cancelled') {
            return response()->json([
                'message' => 'Cancelled appointments cannot be examined.',
            ], 422);
        }

        $appointment = DB::transaction(function () use ($appointment, $doctor, $validated, $prescription) {
            $medicalRecord = MedicalRecord::query()->updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'pet_id' => $appointment->pet_id,
                    'doctor_id' => $doctor->id,
                    'temperature_c' => $validated['temperature_c'] ?? null,
                    'weight_kg' => $validated['weight_kg'] ?? null,
                    'heart_rate_bpm' => $validated['heart_rate_bpm'] ?? null,
                    'symptoms' => $validated['symptoms'] ?? null,
                    'abnormal_signs' => $validated['abnormal_signs'] ?? null,
                    'diagnosis' => $validated['diagnosis'],
                    'preliminary_diagnosis' => $validated['preliminary_diagnosis'] ?? null,
                    'final_diagnosis' => $validated['final_diagnosis'] ?? $validated['diagnosis'],
                    'pathology' => $validated['pathology'] ?? null,
                    'severity_level' => $validated['severity_level'] ?? null,
                    'treatment' => $prescription,
                    'treatment_protocol' => $validated['treatment_protocol'] ?? null,
                    'disease_progress' => $validated['disease_progress'] ?? null,
                    'follow_up_plan' => $validated['follow_up_plan'] ?? null,
                    'service_orders' => $validated['service_orders'] ?? null,
                    'prescriptions' => $validated['prescriptions'] ?? null,
                    'procedures' => $validated['procedures'] ?? null,
                    'progress_logs' => $validated['progress_logs'] ?? null,
                    'signed_off_at' => ! empty($validated['sign_off']) ? Carbon::now() : null,
                    'notes' => $validated['notes'] ?? null,
                    'record_date' => $validated['record_date'] ?? Carbon::today()->toDateString(),
                ],
            );

            if (! $medicalRecord->record_code) {
                $medicalRecord->forceFill([
                    'record_code' => 'MR-' . Carbon::today()->format('Ymd') . '-' . str_pad((string) $medicalRecord->id, 5, '0', STR_PAD_LEFT),
                ])->save();
            }

            $workflowStatus = $validated['workflow_status'] ?? Appointment::WORKFLOW_COMPLETED;

            $appointmentPayload = [
                'status' => $workflowStatus === Appointment::WORKFLOW_COMPLETED ? 'completed' : 'confirmed',
                'workflow_status' => $workflowStatus,
            ];

            if ($workflowStatus === Appointment::WORKFLOW_EXAMINING && ! $appointment->started_at) {
                $appointmentPayload['started_at'] = Carbon::now();
            }

            if ($workflowStatus === Appointment::WORKFLOW_COMPLETED) {
                $appointmentPayload['completed_at'] = Carbon::now();
            }

            if ($workflowStatus === Appointment::WORKFLOW_FOLLOW_UP) {
                $appointmentPayload['follow_up_at'] = $validated['follow_up_at'] ?? $appointment->follow_up_at ?? Carbon::today()->addDays(7);
                $appointmentPayload['status'] = 'confirmed';
            } elseif (! empty($validated['follow_up_at'])) {
                $appointmentPayload['follow_up_at'] = $validated['follow_up_at'];
            }

            $appointment->update($appointmentPayload);

            return $appointment->fresh([
                'pet.species',
                'pet.vaccinations',
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
        $doctor = Doctor::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $doctor) {
            abort(422, 'Your vet account is not linked to a doctor profile yet. Please ask admin/receptionist to map your account in doctor management.');
        }

        return $doctor;
    }
}
