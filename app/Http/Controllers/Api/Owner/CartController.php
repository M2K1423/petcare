<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerCart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = OwnerCart::firstWhere('owner_id', $request->user()->id);
        return response()->json(['data' => $cart?->items ?? []]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'medicine_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
            'medicine' => 'nullable|array',
        ]);

        $cart = OwnerCart::firstOrCreate(['owner_id' => $request->user()->id], ['items' => []]);

        $items = $cart->items ?? [];

        $existingKey = null;
        foreach ($items as $k => $it) {
            if (isset($it['medicine_id']) && $it['medicine_id'] == $validated['medicine_id']) {
                $existingKey = $k;
                break;
            }
        }

        if ($existingKey !== null) {
            if ($validated['quantity'] <= 0) {
                array_splice($items, $existingKey, 1);
            } else {
                $items[$existingKey]['quantity'] = $validated['quantity'];
                if (isset($validated['medicine'])) {
                    $items[$existingKey]['medicine'] = $validated['medicine'];
                }
            }
        } else {
            if ($validated['quantity'] > 0) {
                $items[] = [
                    'medicine_id' => $validated['medicine_id'],
                    'quantity' => $validated['quantity'],
                    'medicine' => $validated['medicine'] ?? null,
                ];
            }
        }

        $cart->items = array_values($items);
        $cart->save();

        return response()->json(['message' => 'Cart updated.', 'data' => $cart->items]);
    }

    public function removeItem(Request $request, int $medicineId): JsonResponse
    {
        $cart = OwnerCart::firstWhere('owner_id', $request->user()->id);
        if (! $cart) {
            return response()->json(['message' => 'Cart is empty.'], 404);
        }

        $items = array_filter($cart->items ?? [], fn($it) => ($it['medicine_id'] ?? null) != $medicineId);
        $cart->items = array_values($items);
        $cart->save();

        return response()->json(['message' => 'Item removed.', 'data' => $cart->items]);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = OwnerCart::firstWhere('owner_id', $request->user()->id);
        if ($cart) {
            $cart->items = [];
            $cart->save();
        }

        return response()->json(['message' => 'Cart cleared.']);
    }
}
