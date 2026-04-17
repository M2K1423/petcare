<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DoctorController extends Controller
{
    /**
     * Lấy danh sách các Bác sĩ đang làm việc và số lịch hẹn đang "treo" (confirmed/pending) của họ 
     * để giúp Lễ tân điều phối ca khám dễ dàng hơn.
     */
    public function available(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->withCount(['appointments as pending_appointments_count' => function ($query) use ($date) {
                // Đếm số lịch chưa hoàn thành của bác sĩ trong ngày
                $query->whereDate('appointment_at', $date)
                      ->whereIn('status', ['pending', 'confirmed']);
            }])
            ->orderBy('pending_appointments_count', 'asc') // Ưu tiên bác sĩ ít việc (ít lịch chờ) xếp lên đầu
            ->get();

        return response()->json([
            'message' => 'Danh sách bác sĩ khả dụng và tải công việc hiện tại.',
            'data' => $doctors
        ]);
    }
}
