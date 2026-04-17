<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Lấy các khoản chưa thanh toán (từ lịch hẹn đã completed nhưng thiếu payment)
     */
    public function unpaid(Request $request)
    {
        // Lấy ra các Appointment đã khám xong (status='completed') nhưng chưa có Payment status = 'paid'
        $unpaidAppointments = Appointment::where('status', 'completed')
            ->whereDoesntHave('payment', function ($query) {
                // Ensure no paid payment exists for the appointment
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
            'message' => 'Danh sách lịch khám chưa thanh toán.',
            'data' => $data
        ]);
    }

    /**
     * Tạo hóa đơn và thanh toán (Billing / Checkout).
     */
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
             return response()->json(['message' => 'Lịch khám chưa hoàn thành không thể thanh toán.'], 400);   
        }

        // Tạo Transaction cho việc thu tiền
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'owner_id' => $appointment->owner_id,
            'service_id' => $appointment->service_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'transaction_code' => 'TXN-' . strtoupper(Str::random(10)),
            'notes' => $validated['notes'] ?? 'Thanh toán trực tiếp tại quầy',
        ]);

        return response()->json([
            'message' => "Thanh toán hóa đơn cho $appointment->owner->name thành công.",
            'data' => $payment
        ], 201);
    }
}
