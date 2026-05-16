<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = MedicineOrder::query()
            ->with([
                'pet:id,name',
                'items.medicine:id,name,unit',
                'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,notes',
                'confirmer:id,name',
            ])
            ->where('owner_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
        ]);

        $pet = Pet::query()
            ->where('id', $validated['pet_id'])
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        $medicineIds = collect($validated['items'])
            ->pluck('medicine_id')
            ->unique()
            ->values();

        $medicines = Medicine::query()
            ->whereKey($medicineIds)
            ->get()
            ->keyBy('id');

        $total = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $medicine = $medicines->get($item['medicine_id']);

            if (! $medicine) {
                return response()->json([
                    'message' => 'Không tìm thấy thuốc.',
                ], 422);
            }

            if ($medicine->stock_quantity < $item['quantity']) {
                return response()->json([
                    'message' => "Không đủ tồn kho cho {$medicine->name}.",
                ], 422);
            }

            $lineTotal = (float) $medicine->price * (int) $item['quantity'];
            $total += $lineTotal;

            $items[] = [
                'medicine_id' => $medicine->id,
                'quantity' => (int) $item['quantity'],
                'unit_price' => $medicine->price,
                'line_total' => $lineTotal,
            ];
        }

        $order = DB::transaction(function () use ($request, $pet, $validated, $items, $total) {
            $order = MedicineOrder::query()->create([
                'owner_id' => $request->user()->id,
                'pet_id' => $pet->id,
                'status' => 'pending',
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            $order->items()->createMany($items);

            return $order->load([
                'pet:id,name',
                'items.medicine:id,name,unit',
            ]);
        });

        return response()->json([
            'message' => 'Đã tạo đơn và đang chờ lễ tân xác nhận.',
            'data' => $order,
        ], 201);
    }

    public function pay(Request $request, MedicineOrder $order): JsonResponse
    {
        $this->authorize('view', $order);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,vnpay,momo',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Allow owner to initiate payment even when order is still 'pending'.
        if (! in_array($order->status, ['pending', 'confirmed', 'paid'], true)) {
            return response()->json([
                'message' => 'Đơn chưa ở trạng thái có thể thanh toán.',
            ], 422);
        }

        $payment = $order->payment;

        // If no payment record exists yet (owner paying immediately), create one.
        if (! $payment) {
            $payment = $order->payment()->create([
                'owner_id' => $request->user()->id,
                'amount' => $order->total_amount,
                'payment_method' => $validated['payment_method'] === 'vnpay' ? 'vnpay' : $validated['payment_method'],
                'gateway' => $validated['payment_method'] === 'vnpay' ? 'vnpay' : null,
                'status' => $validated['payment_method'] === 'vnpay' ? 'pending' : 'created',
                'notes' => $validated['notes'] ?? 'Chủ nuôi khởi tạo thanh toán.',
            ]);
        }

        if ($payment->status === 'paid') {
            return response()->json([
                'message' => 'Đơn này đã được thanh toán.',
                'data' => $order->load([
                    'pet:id,name',
                    'items.medicine:id,name,unit',
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
                'notes' => $validated['notes'] ?? 'Người dùng khởi tạo thanh toán VNPay.',
            ]);

            $paymentUrl = app(\App\Services\VnpayService::class)->createPaymentUrl($payment->fresh(), $request);

            return response()->json([
                'message' => 'Đã tạo thanh toán VNPay thành công.',
                'data' => $order->fresh([
                    'pet:id,name',
                    'items.medicine:id,name,unit',
                    'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
                ]),
                'payment_url' => $paymentUrl,
            ]);
        }

        // non-VNPay immediate capture
        $paidAt = now();

        $payment->update([
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
            'paid_at' => $paidAt,
            'transaction_code' => $payment->transaction_code ?: 'MED-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'notes' => $validated['notes'] ?? 'Payment completed by owner.',
        ]);

        $order->update([
            'status' => 'paid',
            'paid_at' => $paidAt,
        ]);

        return response()->json([
            'message' => 'Đã thu tiền thành công.',
            'data' => $order->fresh([
                'pet:id,name',
                'items.medicine:id,name,unit',
                'payment:id,medicine_order_id,amount,status,payment_method,paid_at,transaction_code,gateway_transaction_no,notes',
            ]),
        ]);
    }
}
