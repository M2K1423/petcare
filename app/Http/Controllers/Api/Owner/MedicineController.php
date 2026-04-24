<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;

class MedicineController extends Controller
{
    public function index(): JsonResponse
    {
        $medicines = Medicine::query()
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $medicines,
        ]);
    }
}
