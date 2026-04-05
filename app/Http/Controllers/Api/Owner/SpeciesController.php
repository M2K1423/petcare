<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\JsonResponse;

class SpeciesController extends Controller
{
    public function index(): JsonResponse
    {
        $species = Species::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'data' => $species,
        ]);
    }
}
