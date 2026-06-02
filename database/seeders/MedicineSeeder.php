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
                'category' => 'Antibiotics',
                'sku' => 'MED-AMX-250',
                'unit' => 'box',
                'stock_quantity' => 25,
                'price' => 120000,
                'expiration_date' => Carbon::today()->addMonths(10),
                'description' => 'Broad-spectrum antibiotic for common bacterial infections.',
                'image_url' => '/images/medicines/amoxicillin.png',
            ],
            [
                'name' => 'Deworming Syrup',
                'category' => 'Digestive Care',
                'sku' => 'MED-DWM-100',
                'unit' => 'bottle',
                'stock_quantity' => 18,
                'price' => 95000,
                'expiration_date' => Carbon::today()->addMonths(8),
                'description' => 'Digestive parasite control for cats and dogs.',
                'image_url' => '/images/medicines/deworming_syrup.png',
            ],
            [
                'name' => 'Skin Care Spray',
                'category' => 'Skin Care',
                'sku' => 'MED-SKN-050',
                'unit' => 'bottle',
                'stock_quantity' => 14,
                'price' => 160000,
                'expiration_date' => Carbon::today()->addMonths(12),
                'description' => 'Topical spray for itch relief and skin support.',
                'image_url' => '/images/medicines/skin_care_spray.png',
            ],
            [
                'name' => 'Eye Care Drops',
                'category' => 'Eye Care',
                'sku' => 'MED-EYE-015',
                'unit' => 'bottle',
                'stock_quantity' => 16,
                'price' => 88000,
                'expiration_date' => Carbon::today()->addMonths(9),
                'description' => 'Gentle drops to soothe irritation and keep pet eyes clean.',
                'image_url' => '/images/medicines/eye_care_drops.png',
            ],
            [
                'name' => 'Joint Support Tablets',
                'category' => 'Bone & Joint',
                'sku' => 'MED-JNT-060',
                'unit' => 'box',
                'stock_quantity' => 20,
                'price' => 210000,
                'expiration_date' => Carbon::today()->addMonths(14),
                'description' => 'Daily joint supplement for senior pets and active breeds.',
                'image_url' => '/images/medicines/joint_support.png',
            ],
            [
                'name' => 'Calming Chews',
                'category' => 'Behavior Support',
                'sku' => 'MED-CLM-030',
                'unit' => 'bag',
                'stock_quantity' => 11,
                'price' => 135000,
                'expiration_date' => Carbon::today()->addMonths(7),
                'description' => 'Soft chews for travel stress, storms, and separation anxiety.',
                'image_url' => '/images/medicines/calming_chews.png',
            ],
            [
                'name' => 'Vitamin Paste',
                'category' => 'Vitamins',
                'sku' => 'MED-VIT-080',
                'unit' => 'tube',
                'stock_quantity' => 22,
                'price' => 99000,
                'expiration_date' => Carbon::today()->addMonths(11),
                'description' => 'Energy and appetite support paste packed with essential vitamins.',
                'image_url' => '/images/medicines/vitamin_paste.png',
            ],
            [
                'name' => 'Ear Cleaning Solution',
                'category' => 'Ear Care',
                'sku' => 'MED-EAR-120',
                'unit' => 'bottle',
                'stock_quantity' => 15,
                'price' => 76000,
                'expiration_date' => Carbon::today()->addMonths(13),
                'description' => 'Routine ear cleanser that helps remove wax and odor safely.',
                'image_url' => '/images/medicines/ear_cleaner.png',
            ],
            [
                'name' => 'Probiotic Powder',
                'category' => 'Digestive Care',
                'sku' => 'MED-PRB-020',
                'unit' => 'box',
                'stock_quantity' => 19,
                'price' => 145000,
                'expiration_date' => Carbon::today()->addMonths(10),
                'description' => 'Supports digestion and recovery after stomach upset or antibiotics.',
                'image_url' => '/images/medicines/probiotic_powder.png',
            ],
            [
                'name' => 'Flea & Tick Spot-On',
                'category' => 'Parasite Control',
                'sku' => 'MED-FLT-003',
                'unit' => 'pack',
                'stock_quantity' => 9,
                'price' => 185000,
                'expiration_date' => Carbon::today()->addMonths(15),
                'description' => 'Monthly topical protection against fleas, ticks, and mites.',
                'image_url' => '/images/medicines/flea_tick.png',
            ],
            [
                'name' => 'Dental Fresh Gel',
                'category' => 'Dental Care',
                'sku' => 'MED-DNT-040',
                'unit' => 'tube',
                'stock_quantity' => 13,
                'price' => 112000,
                'expiration_date' => Carbon::today()->addMonths(6),
                'description' => 'Freshens breath and helps reduce tartar buildup for dogs and cats.',
                'image_url' => '/images/medicines/dental_gel.png',
            ],
            [
                'name' => 'Wound Healing Cream',
                'category' => 'First Aid',
                'sku' => 'MED-WND-025',
                'unit' => 'tube',
                'stock_quantity' => 7,
                'price' => 128000,
                'expiration_date' => Carbon::today()->addMonths(5),
                'description' => 'Protective cream to support minor wound healing and skin recovery.',
                'image_url' => '/images/medicines/wound_cream.png',
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
