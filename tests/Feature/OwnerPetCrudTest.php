<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnerPetCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_crud_only_their_pets(): void
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

        Sanctum::actingAs($owner);

        $createResponse = $this->postJson('/api/owner/pets', [
            'name' => 'Milo',
            'species_id' => $species->id,
            'gender' => 'male',
            'weight' => 4.5,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Milo');

        $petId = $createResponse->json('data.id');

        $this->getJson('/api/owner/pets')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->putJson("/api/owner/pets/{$petId}", [
            'name' => 'Milo Updated',
        ])->assertOk()->assertJsonPath('data.name', 'Milo Updated');

        $this->deleteJson("/api/owner/pets/{$petId}")
            ->assertOk();

        $this->assertDatabaseCount('pets', 0);
    }

    public function test_owner_cannot_view_or_delete_other_owner_pet(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $ownerA = User::factory()->create(['role_id' => $ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $ownerRole->id]);
        $species = Species::query()->create(['name' => 'Cat']);

        $pet = Pet::query()->create([
            'owner_id' => $ownerB->id,
            'species_id' => $species->id,
            'name' => 'Neko',
            'gender' => 'female',
        ]);

        Sanctum::actingAs($ownerA);

        $this->getJson("/api/owner/pets/{$pet->id}")
            ->assertForbidden();

        $this->deleteJson("/api/owner/pets/{$pet->id}")
            ->assertForbidden();
    }

    public function test_non_owner_role_cannot_access_owner_pet_endpoints(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $vetRole = Role::query()->create([
            'name' => 'Vet',
            'slug' => Role::VET,
        ]);

        $owner = User::factory()->create(['role_id' => $ownerRole->id]);
        $vet = User::factory()->create(['role_id' => $vetRole->id]);

        $species = Species::query()->create(['name' => 'Bird']);

        $pet = Pet::query()->create([
            'owner_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'Kiwi',
            'gender' => 'unknown',
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/owner/pets')->assertForbidden();
        $this->postJson('/api/owner/pets', [
            'name' => 'New Pet',
            'species_id' => $species->id,
        ])->assertForbidden();
        $this->getJson("/api/owner/pets/{$pet->id}")->assertForbidden();
    }
}
