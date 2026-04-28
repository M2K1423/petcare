<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = ActivityLog::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->has('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 25);
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($logs);
    }

    public function show(ActivityLog $activityLog)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($activityLog->load('user'));
    }

    public function getUserActivity(Request $request, int $userId)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('per_page', 25);
        $logs = ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($logs);
    }

    public function getEntityHistory(Request $request, string $entityType, int $entityId)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perPage = $request->input('per_page', 50);
        $logs = ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($logs);
    }

    public function getAuditSummary(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $logs = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        $summary = [
            'total_activities' => $logs->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'by_entity_type' => $logs->groupBy('entity_type')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'top_users' => $logs->groupBy('user_id')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
        ];

        return response()->json($summary);
    }

    public function clearOldLogs(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $daysToKeep = $request->input('days', 90);
        $cutoffDate = now()->subDays($daysToKeep);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        ActivityLog::log(
            auth()->id(),
            'delete_logs',
            'ActivityLog',
            0,
            ['cutoff_date' => $cutoffDate, 'days' => $daysToKeep],
            ['deleted_count' => $deletedCount],
            "Cleared activity logs older than {$daysToKeep} days"
        );

        return response()->json([
            'message' => 'Old logs deleted',
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate,
        ]);
    }
}
