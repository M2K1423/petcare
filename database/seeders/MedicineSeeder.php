<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Amoxicillin 250mg',
                'sku' => 'MED-AMX-250',
                'unit' => 'box',
                'stock_quantity' => 25,
                'price' => 120000,
                'expiration_date' => Carbon::today()->addMonths(10),
                'description' => 'Broad-spectrum antibiotic for common bacterial infections.',
            ],
            [
                'name' => 'Deworming Syrup',
                'sku' => 'MED-DWM-100',
                'unit' => 'bottle',
                'stock_quantity' => 18,
                'price' => 95000,
                'expiration_date' => Carbon::today()->addMonths(8),
                'description' => 'Digestive parasite control for cats and dogs.',
            ],
            [
                'name' => 'Skin Care Spray',
                'sku' => 'MED-SKN-050',
                'unit' => 'bottle',
                'stock_quantity' => 14,
                'price' => 160000,
                'expiration_date' => Carbon::today()->addMonths(12),
                'description' => 'Topical spray for itch relief and skin support.',
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::query()->updateOrCreate(
                ['sku' => $medicine['sku']],
                $medicine,
            );
        }
    }
}
