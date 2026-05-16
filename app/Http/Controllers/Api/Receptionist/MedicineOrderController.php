<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\MedicineOrder;
use App\Services\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MedicineOrderController extends Controller
{
    public function __construct(
        private readonly VnpayService $vnpay,
    ) {
    }

    public function index(): JsonResponse
    {
        $orders = MedicineOrder::query()
            ->with([
                'owner:id,name,phone,email',
                'pet:id,name',
                'items.medicine:id,name,unit,stock_quantity',
                'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
            ])
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function confirm(Request $request, MedicineOrder $order): JsonResponse
    {
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Chỉ những đơn đang chờ mới có thể xác nhận.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'nullable|in:cash,credit_card,bank_transfer,vnpay,momo',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = DB::transaction(function () use ($request, $order, $validated) {
            $items = $order->items()->with('medicine')->get();

            foreach ($items as $item) {
                if (($item->medicine?->stock_quantity ?? 0) < $item->quantity) {
                    throw new HttpException(422, "Không đủ tồn kho cho {$item->medicine?->name}.");
                }
            }

            foreach ($items as $item) {
                $item->medicine?->decrement('stock_quantity', $item->quantity);
            }

            $order->update([
                'status' => 'confirmed',
                'confirmed_by' => $request->user()->id,
                'confirmed_at' => Carbon::now(),
                'notes' => $validated['notes'] ?? $order->notes,
            ]);

            $order->payment()->updateOrCreate(
                ['medicine_order_id' => $order->id],
                [
                    'owner_id' => $order->owner_id,
                    'amount' => $order->total_amount,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'gateway' => ($validated['payment_method'] ?? 'cash') === 'vnpay' ? 'vnpay' : null,
                    'status' => 'pending',
                    'notes' => 'Chờ thanh toán cho đơn thuốc đã xác nhận.',
                ],
            );

            return $order->load([
                'owner:id,name,phone,email',
                'pet:id,name',
                'items.medicine:id,name,unit,stock_quantity',
                'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
            ]);
        });

        return response()->json([
            'message' => 'Đã xác nhận đơn và tạo thanh toán chờ.',
            'data' => $order,
        ]);
    }

    public function collect(Request $request, MedicineOrder $order): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,vnpay,momo',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (! in_array($order->status, ['confirmed', 'paid'], true)) {
            return response()->json([
                'message' => 'Đơn phải được xác nhận trước khi thu tiền.',
            ], 422);
        }

        $payment = $order->payment;

        if (! $payment) {
            return response()->json([
                'message' => 'Không tồn tại bản ghi thanh toán cho đơn này.',
            ], 422);
        }

        if ($payment->status === 'paid') {
            return response()->json([
                'message' => 'Đơn này đã được thanh toán.',
                'data' => $order->load([
                    'owner:id,name,phone,email',
                    'pet:id,name',
                    'items.medicine:id,name,unit,stock_quantity',
                    'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
                ]),
            ]);
        }

        if ($validated['payment_method'] === 'vnpay') {
            $payment->update([
                'payment_method' => 'vnpay',
                'gateway' => 'vnpay',
                'status' => 'pending',
                'paid_at' => null,
                'notes' => $validated['notes'] ?? 'Chờ thanh toán qua VNPay.',
            ]);

            $paymentUrl = $this->vnpay->createPaymentUrl($payment->fresh(), $request);

            return response()->json([
                'message' => 'Đã tạo thanh toán VNPay thành công.',
                'data' => $order->fresh([
                    'owner:id,name,phone,email',
                    'pet:id,name',
                    'items.medicine:id,name,unit,stock_quantity',
                    'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
                ]),
                'payment_url' => $paymentUrl,
            ]);
        }

        DB::transaction(function () use ($order, $payment, $validated) {
            $paidAt = Carbon::now();

            $payment->update([
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
                'paid_at' => $paidAt,
                'transaction_code' => $payment->transaction_code ?: 'MED-' . strtoupper(Str::random(10)),
                'notes' => $validated['notes'] ?? 'Đơn thuốc đã được thanh toán tại quầy lễ tân.',
            ]);

            $order->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);
        });

        return response()->json([
            'message' => 'Đã thu tiền thành công.',
            'data' => $order->fresh([
                'owner:id,name,phone,email',
                'pet:id,name',
                'items.medicine:id,name,unit,stock_quantity',
                'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
            ]),
        ]);
    }
}
