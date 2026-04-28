<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['appointment', 'medicineOrder', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('gateway')) {
            $query->where('gateway', $request->gateway);
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
                $q->where('transaction_id', 'like', "%$search%")
                  ->orWhere('reference_id', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $payments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($payments);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return response()->json($payment->load(['appointment', 'medicineOrder', 'user']));
    }

    public function confirm(Request $request, Payment $payment)
    {
        $this->authorize('confirm', $payment);

        $oldStatus = $payment->status;
        $payment->status = 'completed';
        $payment->confirmed_at = now();
        $payment->confirmed_by = auth()->id();
        $payment->save();

        ActivityLog::log(
            auth()->id(),
            'confirm_payment',
            'Payment',
            $payment->id,
            ['status' => $oldStatus],
            ['status' => 'completed'],
            "Confirmed payment: {$payment->transaction_id}"
        );

        return response()->json(['message' => 'Payment confirmed', 'payment' => $payment]);
    }

    public function refund(Request $request, Payment $payment)
    {
        $this->authorize('refund', $payment);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $refundAmount = $validated['amount'] ?? $payment->amount;

        if ($refundAmount > $payment->amount) {
            return response()->json([
                'message' => 'Refund amount cannot exceed payment amount',
            ], 422);
        }

        $oldStatus = $payment->status;
        $payment->status = 'refunded';
        $payment->refund_reason = $validated['reason'];
        $payment->refund_amount = $refundAmount;
        $payment->refunded_at = now();
        $payment->refunded_by = auth()->id();
        $payment->save();

        ActivityLog::log(
            auth()->id(),
            'refund_payment',
            'Payment',
            $payment->id,
            ['status' => $oldStatus, 'amount' => $payment->amount],
            ['status' => 'refunded', 'refund_amount' => $refundAmount],
            "Refunded payment {$payment->transaction_id}: {$validated['reason']}"
        );

        return response()->json(['message' => 'Payment refunded', 'payment' => $payment]);
    }

    public function getStats(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subMonth());
        $dateTo = $request->input('date_to', now());

        $payments = Payment::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->get();

        $stats = [
            'total_amount' => $payments->sum('amount'),
            'total_count' => $payments->count(),
            'by_gateway' => $payments->groupBy('gateway')->map->sum('amount'),
            'by_status' => Payment::whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get()
                ->keyBy('status'),
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
        ];

        return response()->json($stats);
    }

    public function getPendingPayments()
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::where('status', 'pending')
            ->with(['appointment', 'medicineOrder'])
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();

        return response()->json([
            'count' => $payments->count(),
            'payments' => $payments,
        ]);
    }
}
