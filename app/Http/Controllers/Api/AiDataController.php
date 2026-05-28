<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AiDataController extends Controller
{
    /**
     * Xác thực API Key từ AI-service.
     */
    private function verifyApiKey(Request $request)
    {
        $key = $request->header('X-API-Key');
        $expectedKey = env('AI_SERVICE_API_KEY', 'petcare-ai-key');
        
        if ($key !== $expectedKey) {
            abort(403, 'Unauthorized AI Access');
        }
    }

    /**
     * Lấy thông tin khách hàng và thú cưng.
     */
    public function getCustomerInfo(Request $request, $id)
    {
        $this->verifyApiKey($request);

        // ID từ AI-service gửi sang có thể có prefix 'owner_'
        $ownerId = str_replace('owner_', '', $id);
        
        $user = User::with('pets.species')->find($ownerId);
        
        if (!$user) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'pets' => $user->pets->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species->name ?? 'Unknown',
                    'breed' => $pet->breed,
                    'age' => $pet->age,
                    'weight' => $pet->weight,
                    'medical_history' => $pet->medical_history
                ];
            })
        ]);
    }

    /**
     * Lấy danh sách lịch hẹn của khách.
     */
    public function getCustomerAppointments(Request $request, $id)
    {
        $this->verifyApiKey($request);
        
        $ownerId = str_replace('owner_', '', $id);
        $appointments = Appointment::where('owner_id', $ownerId)
            ->with(['pet:id,name', 'vet:id,name', 'service:id,name'])
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get();

        return response()->json(['appointments' => $appointments]);
    }

    /**
     * Lấy danh sách thuốc của phòng khám.
     */
    public function getMedicines(Request $request)
    {
        $this->verifyApiKey($request);

        $limit = $request->query('limit', 50);
        $sort = $request->query('sort', 'name');
        $order = $request->query('order', 'ASC');

        $query = \App\Models\Medicine::query();

        if ($sort === 'price') {
            $query->orderBy('price', $order);
        } else {
            $query->orderBy('name', $order);
        }

        $medicines = $query->take($limit)->get();

        return response()->json([
            'items' => $medicines->map(function ($med) {
                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'category' => $med->category,
                    'sku' => $med->sku,
                    'price' => floatval($med->price),
                    'description' => $med->description,
                    'stock_quantity' => $med->stock_quantity,
                    'unit' => $med->unit,
                ];
            }),
            'total' => $medicines->count()
        ]);
    }

    /**
     * Lấy danh sách dịch vụ khám chữa bệnh.
     */
    public function getServices(Request $request)
    {
        $this->verifyApiKey($request);

        $services = \App\Models\Service::where('is_active', true)->get();

        return response()->json([
            'items' => $services->map(function ($ser) {
                return [
                    'id' => $ser->id,
                    'name' => $ser->name,
                    'description' => $ser->description,
                    'price' => floatval($ser->price),
                    'duration_minutes' => $ser->duration_minutes,
                ];
            }),
            'total' => $services->count()
        ]);
    }
}

