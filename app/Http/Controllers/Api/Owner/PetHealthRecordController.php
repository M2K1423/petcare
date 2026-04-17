<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetHealthRecordController extends Controller
{
    public function show(Request $request, Pet $pet): JsonResponse
    {
        abort_if($pet->owner_id !== $request->user()->id, 403, 'You cannot access this pet record.');

        $pet->load(['species:id,name']);

        $medicalRecords = $pet->medicalRecords()
            ->latest('record_date')
            ->latest('id')
            ->get([
                'id',
                'pet_id',
                'appointment_id',
                'doctor_id',
                'symptoms',
                'diagnosis',
                'treatment',
                'notes',
                'record_date',
                'created_at',
            ]);

        $vaccinations = $pet->vaccinations()
            ->latest('vaccinated_on')
            ->latest('id')
            ->get([
                'id',
                'pet_id',
                'medical_record_id',
                'medicine_id',
                'vaccine_name',
                'vaccinated_on',
                'next_due_on',
                'batch_number',
                'notes',
                'created_at',
            ]);

        return response()->json([
            'data' => [
                'pet' => [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species?->only(['id', 'name']),
                    'breed' => $pet->breed,
                ],
                'medical_records' => $medicalRecords,
                'vaccinations' => $vaccinations,
            ],
        ]);
    }
}
