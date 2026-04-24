<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(): JsonResponse
    {
        $medicines = Medicine::query()
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $medicines,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);

        $medicine = Medicine::query()->create($validated);

        return response()->json([
            'message' => 'Medicine created successfully.',
            'data' => $medicine,
        ], 201);
    }

    public function update(Request $request, Medicine $medicine): JsonResponse
    {
        $validated = $this->validatePayload($request, $medicine);

        $medicine->fill($validated);
        $medicine->save();

        return response()->json([
            'message' => 'Medicine updated successfully.',
            'data' => $medicine,
        ]);
    }

    public function destroy(Medicine $medicine): JsonResponse
    {
        if ($medicine->orderItems()->exists()) {
            return response()->json([
                'message' => 'This medicine is already used in orders and cannot be deleted.',
            ], 422);
        }

        $medicine->delete();

        return response()->json([
            'message' => 'Medicine deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Medicine $medicine = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('medicines', 'sku')->ignore($medicine?->id),
            ],
            'unit' => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:999999'],
            'price' => ['required', 'numeric', 'min:0'],
            'expiration_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
