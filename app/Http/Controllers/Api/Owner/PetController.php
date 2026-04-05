<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Pet::class);

        $pets = Pet::query()
            ->with('species:id,name')
            ->where('owner_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $pets,
        ]);
    }

    public function store(StorePetRequest $request): JsonResponse
    {
        $this->authorize('create', Pet::class);

        $pet = Pet::query()->create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
            'gender' => $request->string('gender')->toString() ?: 'unknown',
        ]);

        return response()->json([
            'message' => 'Pet created successfully.',
            'data' => $pet->load('species:id,name'),
        ], 201);
    }

    public function show(Pet $pet): JsonResponse
    {
        $this->authorize('view', $pet);

        return response()->json([
            'data' => $pet->load('species:id,name'),
        ]);
    }

    public function update(UpdatePetRequest $request, Pet $pet): JsonResponse
    {
        $this->authorize('update', $pet);

        $pet->fill($request->validated());
        $pet->save();

        return response()->json([
            'message' => 'Pet updated successfully.',
            'data' => $pet->load('species:id,name'),
        ]);
    }

    public function destroy(Pet $pet): JsonResponse
    {
        $this->authorize('delete', $pet);

        $pet->delete();

        return response()->json([
            'message' => 'Pet deleted successfully.',
        ]);
    }
}
