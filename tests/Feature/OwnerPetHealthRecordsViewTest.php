<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnerPetHealthRecordsViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_their_pet_health_records_and_vaccinations(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $species = Species::query()->create([
            'name' => 'Dog',
        ]);

        $pet = Pet::query()->create([
            'owner_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'Milo',
            'gender' => 'male',
        ]);

        $record = MedicalRecord::query()->create([
            'pet_id' => $pet->id,
            'record_date' => now()->toDateString(),
            'symptoms' => 'Fever',
            'diagnosis' => 'Flu',
            'treatment' => 'Rest and medicine',
        ]);

        Vaccination::query()->create([
            'pet_id' => $pet->id,
            'medical_record_id' => $record->id,
            'vaccine_name' => 'Rabies',
            'vaccinated_on' => now()->subMonth()->toDateString(),
            'next_due_on' => now()->addMonths(11)->toDateString(),
        ]);

        Sanctum::actingAs($owner);

        $this->getJson("/api/owner/pets/{$pet->id}/health-records")
            ->assertOk()
            ->assertJsonPath('data.pet.id', $pet->id)
            ->assertJsonPath('data.pet.name', 'Milo')
            ->assertJsonCount(1, 'data.medical_records')
            ->assertJsonCount(1, 'data.vaccinations')
            ->assertJsonPath('data.vaccinations.0.vaccine_name', 'Rabies');
    }

    public function test_owner_cannot_view_other_owner_pet_health_records(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $ownerA = User::factory()->create(['role_id' => $ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $ownerRole->id]);

        $species = Species::query()->create([
            'name' => 'Cat',
        ]);

        $petB = Pet::query()->create([
            'owner_id' => $ownerB->id,
            'species_id' => $species->id,
            'name' => 'Neko',
            'gender' => 'female',
        ]);

        Sanctum::actingAs($ownerA);

        $this->getJson("/api/owner/pets/{$petB->id}/health-records")
            ->assertForbidden();
    }
}
