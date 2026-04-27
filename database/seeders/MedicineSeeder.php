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
                'image_url' => 'https://placehold.co/320x220/E8F3FF/2A6496?text=Amoxicillin',
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
                'image_url' => 'https://placehold.co/320x220/FDF2E9/B45309?text=Deworming+Syrup',
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
                'image_url' => 'https://placehold.co/320x220/ECFDF3/027A48?text=Skin+Care+Spray',
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
                'image_url' => 'https://placehold.co/320x220/EFF6FF/1D4ED8?text=Eye+Care+Drops',
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
                'image_url' => 'https://placehold.co/320x220/F5FAFF/5078A0?text=Joint+Support',
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
                'image_url' => 'https://placehold.co/320x220/FFF7ED/C2410C?text=Calming+Chews',
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
                'image_url' => 'https://placehold.co/320x220/FEFCE8/A16207?text=Vitamin+Paste',
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
                'image_url' => 'https://placehold.co/320x220/F0FDFA/0F766E?text=Ear+Cleaner',
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
                'image_url' => 'https://placehold.co/320x220/F7FEE7/4D7C0F?text=Probiotic+Powder',
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
                'image_url' => 'https://placehold.co/320x220/FEF2F2/B91C1C?text=Flea+%26+Tick',
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
                'image_url' => 'https://placehold.co/320x220/F5F3FF/6D28D9?text=Dental+Gel',
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
                'image_url' => 'https://placehold.co/320x220/EEF2FF/4338CA?text=Wound+Cream',
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
