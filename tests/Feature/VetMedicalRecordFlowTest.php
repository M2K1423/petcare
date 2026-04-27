<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VetMedicalRecordFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_vet_can_view_only_their_appointments_and_save_medical_record(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Owner',
            'slug' => Role::OWNER,
        ]);

        $vetRole = Role::query()->create([
            'name' => 'Vet',
            'slug' => Role::VET,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $vetUser = User::factory()->create([
            'role_id' => $vetRole->id,
        ]);

        $otherVetUser = User::factory()->create([
            'role_id' => $vetRole->id,
        ]);

        $vetDoctor = Doctor::query()->create([
            'user_id' => $vetUser->id,
            'license_number' => 'VET-001',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        $otherVetDoctor = Doctor::query()->create([
            'user_id' => $otherVetUser->id,
            'license_number' => 'VET-002',
            'specialty' => 'General',
            'is_active' => true,
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

        $myAppointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $owner->id,
            'doctor_id' => $vetDoctor->id,
            'appointment_at' => now()->addHour(),
            'status' => 'confirmed',
            'reason' => 'Coughing',
        ]);

        $otherAppointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $owner->id,
            'doctor_id' => $otherVetDoctor->id,
            'appointment_at' => now()->addHours(2),
            'status' => 'confirmed',
            'reason' => 'Sneezing',
        ]);

        Sanctum::actingAs($vetUser);

        $this->getJson('/api/vet/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $myAppointment->id);

        $this->getJson("/api/vet/appointments/{$myAppointment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $myAppointment->id);

        $this->getJson("/api/vet/appointments/{$otherAppointment->id}")
            ->assertForbidden();

        $this->putJson("/api/vet/appointments/{$myAppointment->id}/medical-record", [
            'record_date' => now()->toDateString(),
            'symptoms' => 'Dry cough for two days',
            'diagnosis' => 'Mild upper respiratory infection',
            'prescription' => 'Supportive care and oral medication',
            'notes' => 'Return if symptoms worsen',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.medical_record.diagnosis', 'Mild upper respiratory infection');

        $this->assertDatabaseHas('medical_records', [
            'appointment_id' => $myAppointment->id,
            'doctor_id' => $vetDoctor->id,
            'diagnosis' => 'Mild upper respiratory infection',
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $myAppointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_non_vet_role_cannot_access_vet_medical_record_endpoints(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Owner',
            'slug' => Role::OWNER,
        ]);

        $vetRole = Role::query()->create([
            'name' => 'Vet',
            'slug' => Role::VET,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $vetUser = User::factory()->create([
            'role_id' => $vetRole->id,
        ]);

        $vetDoctor = Doctor::query()->create([
            'user_id' => $vetUser->id,
            'license_number' => 'VET-003',
            'specialty' => 'General',
            'is_active' => true,
        ]);

        $species = Species::query()->create([
            'name' => 'Cat',
        ]);

        $pet = Pet::query()->create([
            'owner_id' => $owner->id,
            'species_id' => $species->id,
            'name' => 'Luna',
            'gender' => 'female',
        ]);

        $appointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $owner->id,
            'doctor_id' => $vetDoctor->id,
            'appointment_at' => now()->addHour(),
            'status' => 'confirmed',
            'reason' => 'Check-up',
        ]);

        Sanctum::actingAs($owner);

        $this->getJson('/api/vet/appointments')->assertForbidden();
        $this->getJson("/api/vet/appointments/{$appointment->id}")->assertForbidden();
        $this->putJson("/api/vet/appointments/{$appointment->id}/medical-record", [
            'diagnosis' => 'Blocked',
            'prescription' => 'Blocked',
        ])->assertForbidden();
    }
}
