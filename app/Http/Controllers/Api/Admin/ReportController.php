<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function appointmentStats(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $appointments = Appointment::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        $stats = [
            'total' => $appointments->count(),
            'by_status' => $appointments->groupBy('status')->map->count(),
            'by_doctor' => $appointments->groupBy('doctor_id')->map->count(),
            'by_service' => $appointments->groupBy('service_id')->map->count(),
            'completed_today' => Appointment::whereDate('appointment_at', now())
                ->where('status', 'completed')
                ->count(),
            'pending_today' => Appointment::whereDate('appointment_at', now())
                ->where('status', 'pending')
                ->count(),
        ];

        return response()->json($stats);
    }

    public function revenueReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $payments = Payment::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->get();

        $report = [
            'total_revenue' => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'average_transaction' => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
            'by_gateway' => $payments->groupBy('gateway')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                    'average' => $items->count() > 0 ? $items->sum('amount') / $items->count() : 0,
                ];
            }),
            'daily_revenue' => $payments->groupBy(fn($p) => $p->created_at->toDateString())
                ->map->sum('amount'),
        ];

        return response()->json($report);
    }

    public function doctorPerformance(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $doctors = Doctor::with('user')
            ->withCount(['appointments' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            }])
            ->orderByDesc('appointments_count')
            ->get();

        $performance = $doctors->map(function ($doctor) use ($dateFrom, $dateTo) {
            $appointments = Appointment::where('doctor_id', $doctor->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();

            return [
                'doctor_id' => $doctor->id,
                'doctor_name' => $doctor->user?->name,
                'specialty' => $doctor->specialty,
                'total_appointments' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'pending' => $appointments->where('status', 'pending')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
            ];
        });

        return response()->json($performance);
    }

    public function servicePopularity(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $services = Appointment::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('service')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                $service = $items->first()?->service;
                return [
                    'service_id' => $service?->id,
                    'service_name' => $service?->name,
                    'count' => $items->count(),
                    'revenue' => $items->sum(fn($a) => $a->service?->price ?? 0),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return response()->json($services);
    }

    public function customerStats(Request $request)
    {
        $totalCustomers = User::where('role_id', function ($q) {
            $q->select('id')->from('roles')->where('slug', 'owner')->limit(1);
        })->count();

        $totalPets = Pet::count();
        $appointmentsPerCustomer = Appointment::count() / max($totalCustomers, 1);

        $stats = [
            'total_customers' => $totalCustomers,
            'new_customers_this_month' => User::where('role_id', function ($q) {
                $q->select('id')->from('roles')->where('slug', 'owner')->limit(1);
            })->whereMonth('created_at', now()->month)->count(),
            'total_pets' => $totalPets,
            'average_pets_per_customer' => $totalCustomers > 0 ? $totalPets / $totalCustomers : 0,
            'total_appointments' => Appointment::count(),
            'average_appointments_per_customer' => $appointmentsPerCustomer,
        ];

        return response()->json($stats);
    }

    public function topServices(Request $request)
    {
        $limit = $request->input('limit', 10);
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $topServices = Appointment::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('service_id')
            ->with('service')
            ->get()
            ->groupBy('service_id')
            ->map(function ($items) {
                $service = $items->first()?->service;
                return [
                    'service_id' => $service?->id,
                    'service_name' => $service?->name,
                    'count' => $items->count(),
                    'revenue' => $items->sum(fn($a) => $a->service?->price ?? 0),
                ];
            })
            ->sortByDesc('count')
            ->take($limit)
            ->values();

        return response()->json($topServices);
    }
}
