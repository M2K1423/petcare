<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $speciesList = [
            [
                'name' => 'Dog',
                'scientific_name' => 'Canis lupus familiaris',
                'description' => 'Domestic dog',
            ],
            [
                'name' => 'Cat',
                'scientific_name' => 'Felis catus',
                'description' => 'Domestic cat',
            ],
            [
                'name' => 'Rabbit',
                'scientific_name' => 'Oryctolagus cuniculus',
                'description' => 'Domestic rabbit',
            ],
            [
                'name' => 'Bird',
                'scientific_name' => 'Aves',
                'description' => 'Pet bird',
            ],
        ];

        foreach ($speciesList as $species) {
            Species::query()->updateOrCreate(
                ['name' => $species['name']],
                $species,
            );
        }
    }
}
