<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfExportController extends Controller
{
    private function getClinicInfo()
    {
        return [
            'name' => SystemSetting::getSetting('clinic_name', 'Pet Care Clinic'),
            'address' => SystemSetting::getSetting('clinic_address', '123 Đường Thú Y, TP.HCM'),
            'phone' => SystemSetting::getSetting('clinic_phone', '0901234567'),
            'email' => SystemSetting::getSetting('clinic_email', 'contact@petcare.com'),
        ];
    }

    public function exportPrescription(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        if ($user->hasRole('vet')) {
            $doctor = $user->doctor;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json(['message' => 'Bạn không có quyền in đơn thuốc của ca khám này.'], 403);
            }
        }

        $appointment->load(['pet.species', 'owner', 'medicalRecord', 'doctor.user']);

        if (!$appointment->medicalRecord || (empty($appointment->medicalRecord->treatment) && empty($appointment->medicalRecord->prescriptions))) {
            return response()->json(['message' => 'Bệnh án này chưa có đơn thuốc.'], 404);
        }

        $data = [
            'clinic' => $this->getClinicInfo(),
            'appointment' => $appointment,
            'record' => $appointment->medicalRecord,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdf.prescription', $data);
        return $pdf->stream('don_thuoc_' . $appointment->id . '.pdf');
    }

    public function exportInvoice(Request $request, Payment $payment)
    {
        $payment->load([
            'appointment.pet.species',
            'appointment.owner',
            'appointment.service',
            'medicineOrder.items.medicine',
            'medicineOrder.owner',
            'medicineOrder.pet'
        ]);

        $data = [
            'clinic' => $this->getClinicInfo(),
            'payment' => $payment,
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        return $pdf->stream('hoa_don_' . $payment->id . '.pdf');
    }
}
