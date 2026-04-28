<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Pet;
use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PetController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Pet::with('owner', 'species');

        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->has('species_id')) {
            $query->where('species_id', $request->species_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('breed', 'like', "%$search%")
                  ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%$search%"));
            });
        }

        $perPage = $request->input('per_page', 15);
        $pets = $query->paginate($perPage);

        return response()->json($pets);
    }

    public function show(Pet $pet)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($pet->load(['owner', 'species', 'appointments', 'medicalRecords', 'vaccinations']));
    }

    public function getOwnerPets(Request $request, int $ownerId)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pets = Pet::where('owner_id', $ownerId)
            ->with('species')
            ->get();

        return response()->json($pets);
    }

    public function getPetAppointments(Pet $pet)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::where('pet_id', $pet->id)
            ->with('doctor', 'service')
            ->orderBy('appointment_at', 'desc')
            ->paginate(10);

        return response()->json($appointments);
    }

    public function getPetHealthRecords(Pet $pet)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $records = $pet->medicalRecords()
            ->with('doctor')
            ->orderBy('record_date', 'desc')
            ->paginate(10);

        return response()->json($records);
    }

    public function getStats()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_pets' => Pet::count(),
            'total_owners' => Pet::distinct('owner_id')->count(),
            'by_species' => Pet::select('species_id')
                ->with('species')
                ->get()
                ->groupBy('species.name')
                ->map->count(),
            'by_gender' => Pet::select('gender')
                ->get()
                ->groupBy('gender')
                ->map->count(),
            'average_age_months' => Pet::whereNotNull('birth_date')
                ->get()
                ->map(fn($p) => now()->diffInMonths($p->birth_date))
                ->average(),
        ];

        return response()->json($stats);
    }
}
