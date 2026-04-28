<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Medicine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Medicine::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $inventory = $query->paginate($perPage);

        return response()->json($inventory);
    }

    public function importStock(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $medicine = Medicine::find($validated['medicine_id']);
        $oldQuantity = $medicine->stock_quantity;
        $medicine->stock_quantity += $validated['quantity'];
        $medicine->save();

        ActivityLog::log(
            auth()->id(),
            'import_stock',
            'Medicine',
            $medicine->id,
            ['stock_quantity' => $oldQuantity],
            ['stock_quantity' => $medicine->stock_quantity],
            "Imported {$validated['quantity']} units of {$medicine->name} from {$validated['supplier']}"
        );

        return response()->json([
            'message' => 'Stock imported successfully',
            'medicine' => $medicine,
            'quantity_added' => $validated['quantity'],
            'new_quantity' => $medicine->stock_quantity,
        ]);
    }

    public function exportStock(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'export_type' => 'required|in:treatment,sale',
            'notes' => 'nullable|string',
        ]);

        $medicine = Medicine::find($validated['medicine_id']);

        if ($medicine->stock_quantity < $validated['quantity']) {
            return response()->json([
                'message' => 'Insufficient stock',
                'available' => $medicine->stock_quantity,
                'requested' => $validated['quantity'],
            ], 422);
        }

        $oldQuantity = $medicine->stock_quantity;
        $medicine->stock_quantity -= $validated['quantity'];
        $medicine->save();

        ActivityLog::log(
            auth()->id(),
            'export_stock',
            'Medicine',
            $medicine->id,
            ['stock_quantity' => $oldQuantity],
            ['stock_quantity' => $medicine->stock_quantity],
            "Exported {$validated['quantity']} units of {$medicine->name} for {$validated['export_type']}"
        );

        return response()->json([
            'message' => 'Stock exported successfully',
            'medicine' => $medicine,
            'quantity_exported' => $validated['quantity'],
            'export_type' => $validated['export_type'],
            'remaining_quantity' => $medicine->stock_quantity,
        ]);
    }

    public function getInventoryValue()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medicines = Medicine::get();

        $totalValue = $medicines->sum(fn($m) => $m->stock_quantity * $m->price);
        $totalItems = $medicines->sum('stock_quantity');
        $uniqueMedicines = $medicines->count();

        $valueByCategory = $medicines->groupBy('category')->map(function ($items) {
            return $items->sum(fn($m) => $m->stock_quantity * $m->price);
        });

        return response()->json([
            'total_value' => $totalValue,
            'total_items' => $totalItems,
            'unique_medicines' => $uniqueMedicines,
            'average_value_per_medicine' => $uniqueMedicines > 0 ? $totalValue / $uniqueMedicines : 0,
            'by_category' => $valueByCategory,
        ]);
    }

    public function getLowStockAlert()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $threshold = request()->input('threshold', 10);
        $lowStockMedicines = Medicine::where('stock_quantity', '<', $threshold)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'count' => $lowStockMedicines->count(),
            'medicines' => $lowStockMedicines,
        ]);
    }

    public function getExpirationAlert()
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $daysThreshold = request()->input('days', 30);
        $expiringMedicines = Medicine::whereBetween('expiration_date', [now(), now()->addDays($daysThreshold)])
            ->orderBy('expiration_date', 'asc')
            ->get();

        $expiredMedicines = Medicine::where('expiration_date', '<', now())->get();

        return response()->json([
            'days_threshold' => $daysThreshold,
            'expiring_count' => $expiringMedicines->count(),
            'expiring_medicines' => $expiringMedicines,
            'expired_count' => $expiredMedicines->count(),
            'expired_medicines' => $expiredMedicines,
        ]);
    }

    public function getInventoryReport(Request $request)
    {
        if (!auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $medicines = Medicine::get();

        $report = [
            'summary' => [
                'total_medicines' => $medicines->count(),
                'total_stock_value' => $medicines->sum(fn($m) => $m->stock_quantity * $m->price),
                'total_items' => $medicines->sum('stock_quantity'),
            ],
            'status' => [
                'in_stock' => $medicines->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => $medicines->where('stock_quantity', 0)->count(),
                'low_stock' => $medicines->where('stock_quantity', '<', 10)->count(),
                'expiring_soon' => $medicines->filter(fn($m) => $m->expiration_date && $m->expiration_date->diffInDays(now()) <= 30)->count(),
                'expired' => $medicines->filter(fn($m) => $m->expiration_date && $m->expiration_date < now())->count(),
            ],
            'by_category' => $medicines->groupBy('category')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total_items' => $items->sum('stock_quantity'),
                    'total_value' => $items->sum(fn($m) => $m->stock_quantity * $m->price),
                ];
            }),
        ];

        return response()->json($report);
    }
}
