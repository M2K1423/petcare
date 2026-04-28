<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Doctor;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Doctor::class);

        $query = Doctor::with('user.role');

        if ($request->has('specialty')) {
            $query->where('specialty', $request->specialty);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_number', 'like', "%$search%")
                  ->orWhere('specialty', 'like', "%$search%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%$search%"));
            });
        }

        $perPage = $request->input('per_page', 15);
        $doctors = $query->paginate($perPage);

        return response()->json($doctors);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Doctor::class);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id|unique:doctors,user_id',
            'license_number' => 'required|string|unique:doctors,license_number',
            'specialty' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'years_of_experience' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $doctor = Doctor::create($validated);

        ActivityLog::log(
            auth()->id(),
            'create',
            'Doctor',
            $doctor->id,
            [],
            $doctor->only(['user_id', 'license_number', 'specialty', 'years_of_experience']),
            "Created doctor: {$doctor->license_number}"
        );

        return response()->json($doctor->load('user'), Response::HTTP_CREATED);
    }

    public function show(Doctor $doctor)
    {
        $this->authorize('view', $doctor);
        return response()->json($doctor->load('user.role'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $validated = $request->validate([
            'license_number' => 'sometimes|string|unique:doctors,license_number,' . $doctor->id,
            'specialty' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'years_of_experience' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldValues = $doctor->only(['license_number', 'specialty', 'years_of_experience', 'is_active']);
        $doctor->update($validated);
        $newValues = $doctor->only(['license_number', 'specialty', 'years_of_experience', 'is_active']);

        ActivityLog::log(
            auth()->id(),
            'update',
            'Doctor',
            $doctor->id,
            $oldValues,
            $newValues,
            "Updated doctor: {$doctor->license_number}"
        );

        return response()->json($doctor->load('user'));
    }

    public function destroy(Doctor $doctor)
    {
        $this->authorize('delete', $doctor);

        $doctorId = $doctor->id;
        $licenseNumber = $doctor->license_number;

        $doctor->delete();

        ActivityLog::log(
            auth()->id(),
            'delete',
            'Doctor',
            $doctorId,
            $doctor->only(['license_number', 'specialty']),
            [],
            "Deleted doctor: {$licenseNumber}"
        );

        return response()->json(['message' => 'Doctor deleted successfully']);
    }

    public function getSpecialties()
    {
        $specialties = Doctor::distinct()
            ->pluck('specialty')
            ->sort();

        return response()->json($specialties);
    }
}
