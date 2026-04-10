<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnerAppointmentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_list_and_cancel_appointment(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $species = Species::query()->create(['name' => 'Dog']);

        $pet = Pet::query()->create([
            'owner_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'Milo',
            'gender' => 'male',
        ]);

        Sanctum::actingAs($owner);

        $createResponse = $this->postJson('/api/owner/appointments', [
            'pet_id' => $pet->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '09:30',
            'reason' => 'Routine check',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.pet.id', $pet->id)
            ->assertJsonPath('data.status', 'pending');

        $appointmentId = $createResponse->json('data.id');

        $this->getJson('/api/owner/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->deleteJson("/api/owner/appointments/{$appointmentId}")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_owner_cannot_create_appointment_for_other_owner_pet(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $ownerA = User::factory()->create(['role_id' => $ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $ownerRole->id]);
        $species = Species::query()->create(['name' => 'Cat']);

        $otherPet = Pet::query()->create([
            'owner_id' => $ownerB->id,
            'species_id' => $species->id,
            'name' => 'Neko',
            'gender' => 'female',
        ]);

        Sanctum::actingAs($ownerA);

        $this->postJson('/api/owner/appointments', [
            'pet_id' => $otherPet->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '14:00',
        ])->assertForbidden();
    }

    public function test_owner_cannot_cancel_other_owner_appointment(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $ownerA = User::factory()->create(['role_id' => $ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $ownerRole->id]);
        $species = Species::query()->create(['name' => 'Bird']);

        $petB = Pet::query()->create([
            'owner_id' => $ownerB->id,
            'species_id' => $species->id,
            'name' => 'Kiwi',
            'gender' => 'unknown',
        ]);

        $appointment = Appointment::query()->create([
            'pet_id' => $petB->id,
            'owner_id' => $ownerB->id,
            'appointment_at' => now()->addDay(),
            'status' => 'pending',
        ]);

        Sanctum::actingAs($ownerA);

        $this->deleteJson("/api/owner/appointments/{$appointment->id}")
            ->assertForbidden();
    }
}
