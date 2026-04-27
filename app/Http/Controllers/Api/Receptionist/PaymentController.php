<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Services\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private readonly VnpayService $vnpay,
    ) {
    }

    public function unpaid(Request $request)
    {
        $unpaidAppointments = Appointment::where('status', 'completed')
            ->whereDoesntHave('payment', function ($query) {
                $query->where('status', 'paid');
            })
            ->with(['pet.species', 'owner', 'service', 'doctor.user', 'medicalRecord'])
            ->get();

        $data = $unpaidAppointments->map(function (Appointment $appointment) {
            $record = $appointment->medicalRecord;

            return [
                'id' => $appointment->id,
                'appointment_id' => $appointment->id,
                'diagnosis' => $record?->diagnosis,
                'total_cost' => (float) ($appointment->service?->price ?? 0),
                'appointment' => [
                    'pet' => $appointment->pet,
                    'owner' => $appointment->owner,
                    'doctor' => $appointment->doctor,
                    'service' => $appointment->service,
                    'appointment_at' => $appointment->appointment_at,
                    'status' => $appointment->status,
                ],
            ];
        });

        return response()->json([
            'message' => 'Danh sach lich kham chua thanh toan.',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,vnpay,momo',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::with('owner')->findOrFail($validated['appointment_id']);

        if ($appointment->status !== 'completed') {
            return response()->json([
                'message' => 'Lich kham chua hoan thanh khong the thanh toan.',
            ], 400);
        }

        $existingPaidPayment = Payment::query()
            ->where('appointment_id', $appointment->id)
            ->where('status', 'paid')
            ->first();

        if ($existingPaidPayment) {
            return response()->json([
                'message' => 'Appointment has already been paid.',
                'data' => $existingPaidPayment,
            ], 422);
        }

        if ($validated['payment_method'] === 'vnpay') {
            $payment = Payment::query()->updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'owner_id' => $appointment->owner_id,
                    'service_id' => $appointment->service_id,
                    'amount' => $validated['amount'],
                    'payment_method' => 'vnpay',
                    'gateway' => 'vnpay',
                    'status' => 'pending',
                    'paid_at' => null,
                    'notes' => $validated['notes'] ?? 'Cho thanh toan qua VNPay.',
                ],
            );

            return response()->json([
                'message' => 'VNPay payment created successfully.',
                'data' => $payment,
                'payment_url' => $this->vnpay->createPaymentUrl($payment, $request),
            ], 201);
        }

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'owner_id' => $appointment->owner_id,
            'service_id' => $appointment->service_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'transaction_code' => 'TXN-' . strtoupper(Str::random(10)),
            'notes' => $validated['notes'] ?? 'Thanh toan truc tiep tai quay',
        ]);

        return response()->json([
            'message' => "Thanh toan hoa don cho {$appointment->owner->name} thanh cong.",
            'data' => $payment,
        ], 201);
    }
}
