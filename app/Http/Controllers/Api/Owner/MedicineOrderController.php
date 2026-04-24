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
            ->whereIn('id', $medicineIds)
            ->get()
            ->keyBy('id');

        $total = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $medicine = $medicines->get($item['medicine_id']);

            if (! $medicine) {
                return response()->json([
                    'message' => 'Medicine not found.',
                ], 422);
            }

            if ($medicine->stock_quantity < $item['quantity']) {
                return response()->json([
                    'message' => "Insufficient stock for {$medicine->name}.",
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
            'message' => 'Order created successfully and is waiting for receptionist confirmation.',
            'data' => $order,
        ], 201);
    }
}
