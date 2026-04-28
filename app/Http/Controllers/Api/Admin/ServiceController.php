<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Service;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);

        $query = Service::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $services = $query->paginate($perPage);

        return response()->json($services);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $validated = $request->validate([
            'name' => 'required|string|unique:services,name|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:15',
            'is_active' => 'boolean',
        ]);

        $service = Service::create($validated);

        ActivityLog::log(
            auth()->id(),
            'create',
            'Service',
            $service->id,
            [],
            $service->only(['name', 'price', 'duration_minutes']),
            "Created service: {$service->name}"
        );

        return response()->json($service, Response::HTTP_CREATED);
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return response()->json($service);
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:services,name,' . $service->id . '|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_minutes' => 'sometimes|integer|min:15',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldValues = $service->only(['name', 'price', 'duration_minutes', 'is_active']);
        $service->update($validated);
        $newValues = $service->only(['name', 'price', 'duration_minutes', 'is_active']);

        ActivityLog::log(
            auth()->id(),
            'update',
            'Service',
            $service->id,
            $oldValues,
            $newValues,
            "Updated service: {$service->name}"
        );

        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $serviceId = $service->id;
        $serviceName = $service->name;

        $service->delete();

        ActivityLog::log(
            auth()->id(),
            'delete',
            'Service',
            $serviceId,
            $service->only(['name', 'price']),
            [],
            "Deleted service: {$serviceName}"
        );

        return response()->json(['message' => 'Service deleted successfully']);
    }

    public function toggle(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $oldValue = $service->is_active;
        $service->is_active = !$service->is_active;
        $service->save();

        ActivityLog::log(
            auth()->id(),
            'toggle',
            'Service',
            $service->id,
            ['is_active' => $oldValue],
            ['is_active' => $service->is_active],
            "Toggled service status: {$service->name} → " . ($service->is_active ? 'Active' : 'Inactive')
        );

        return response()->json(['message' => 'Service status toggled', 'service' => $service]);
    }

    public function updatePrice(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $oldPrice = $service->price;
        $service->price = $validated['price'];
        $service->save();

        ActivityLog::log(
            auth()->id(),
            'update_price',
            'Service',
            $service->id,
            ['price' => $oldPrice],
            ['price' => $service->price],
            "Updated price for service: {$service->name} ({$oldPrice} → {$service->price})"
        );

        return response()->json(['message' => 'Service price updated', 'service' => $service]);
    }
}
