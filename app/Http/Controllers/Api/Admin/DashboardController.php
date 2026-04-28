<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $dashboard = [
            'summary' => $this->getSummary(),
            'today_stats' => $this->getTodayStats(),
            'revenue' => $this->getRevenueStats(),
            'alerts' => $this->getAlerts(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        return response()->json($dashboard);
    }

    private function getSummary(): array
    {
        return [
            'total_users' => User::count(),
            'total_doctors' => Doctor::count(),
            'total_pets' => Pet::count(),
            'total_appointments' => Appointment::count(),
            'active_users' => User::where('is_locked', false)->count(),
            'locked_users' => User::where('is_locked', true)->count(),
        ];
    }

    private function getTodayStats(): array
    {
        $today = now()->toDateString();
        
        return [
            'appointments_today' => Appointment::whereDate('appointment_at', $today)->count(),
            'appointments_completed_today' => Appointment::whereDate('appointment_at', $today)
                ->where('status', 'completed')
                ->count(),
            'appointments_pending_today' => Appointment::whereDate('appointment_at', $today)
                ->where('status', 'pending')
                ->count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'new_pets_today' => Pet::whereDate('created_at', $today)->count(),
            'new_payments_today' => Payment::whereDate('created_at', $today)->count(),
        ];
    }

    private function getRevenueStats(): array
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth();
        $thisYear = now()->startOfYear();

        return [
            'today' => Payment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'this_month' => Payment::where('created_at', '>=', $thisMonth)
                ->where('status', 'completed')
                ->sum('amount'),
            'this_year' => Payment::where('created_at', '>=', $thisYear)
                ->where('status', 'completed')
                ->sum('amount'),
            'total_all_time' => Payment::where('status', 'completed')->sum('amount'),
        ];
    }

    private function getAlerts(): array
    {
        $alerts = [];

        // Low stock medicines
        $lowStockCount = Medicine::where('stock_quantity', '<', 10)->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} medicines have low stock",
                'action' => '/admin/medicines/low-stock',
            ];
        }

        // Expiring medicines
        $expiringCount = Medicine::whereBetween('expiration_date', [now(), now()->addDays(30)])->count();
        if ($expiringCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Expiring Medicines',
                'message' => "{$expiringCount} medicines are expiring within 30 days",
                'action' => '/admin/medicines/expiring',
            ];
        }

        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Payments',
                'message' => "{$pendingPayments} payments awaiting confirmation",
                'action' => '/admin/payments?status=pending',
            ];
        }

        // Locked users
        $lockedUsers = User::where('is_locked', true)->count();
        if ($lockedUsers > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Locked Users',
                'message' => "{$lockedUsers} user accounts are locked",
                'action' => '/admin/users?locked=true',
            ];
        }

        return $alerts;
    }

    private function getRecentActivity(): array
    {
        return \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user?->name,
                    'action' => $log->action,
                    'entity_type' => $log->entity_type,
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                ];
            })
            ->toArray();
    }

    public function stats(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $period = $request->input('period', 'month'); // week, month, year

        $stats = match ($period) {
            'week' => $this->getWeeklyStats(),
            'year' => $this->getYearlyStats(),
            default => $this->getMonthlyStats(),
        };

        return response()->json($stats);
    }

    private function getMonthlyStats(): array
    {
        $thisMonth = now()->startOfMonth();
        $today = now();

        $dailyStats = [];
        for ($i = 0; $i < $today->day; $i++) {
            $date = $thisMonth->copy()->addDays($i)->toDateString();
            $dailyStats[$date] = [
                'appointments' => Appointment::whereDate('appointment_at', $date)->count(),
                'revenue' => Payment::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'new_users' => User::whereDate('created_at', $date)->count(),
            ];
        }

        return $dailyStats;
    }

    private function getWeeklyStats(): array
    {
        $weekStart = now()->startOfWeek();
        $dailyStats = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            $dailyStats[$date] = [
                'appointments' => Appointment::whereDate('appointment_at', $date)->count(),
                'revenue' => Payment::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'new_users' => User::whereDate('created_at', $date)->count(),
            ];
        }

        return $dailyStats;
    }

    private function getYearlyStats(): array
    {
        $yearlyStats = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthStart = now()->setMonth($i)->startOfMonth();
            $monthEnd = now()->setMonth($i)->endOfMonth();

            $yearlyStats["month_$i"] = [
                'month' => $monthStart->format('F'),
                'appointments' => Appointment::whereBetween('appointment_at', [$monthStart, $monthEnd])->count(),
                'revenue' => Payment::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('status', 'completed')
                    ->sum('amount'),
                'new_users' => User::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        return $yearlyStats;
    }
}
