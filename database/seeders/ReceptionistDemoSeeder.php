<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Service;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReceptionistDemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()->pluck('id', 'slug');

        $dog = Species::query()->where('name', 'Dog')->firstOrFail();
        $cat = Species::query()->where('name', 'Cat')->firstOrFail();

        $vetOneUser = User::query()->updateOrCreate(
            ['email' => 'vet.lan@example.com'],
            [
                'name' => 'Dr. Lan',
                'phone' => '0901000001',
                'password' => 'password',
                'role_id' => $roles[Role::VET] ?? null,
            ],
        );

        $vetTwoUser = User::query()->updateOrCreate(
            ['email' => 'vet.minh@example.com'],
            [
                'name' => 'Dr. Minh',
                'phone' => '0901000002',
                'password' => 'password',
                'role_id' => $roles[Role::VET] ?? null,
            ],
        );

        $doctorOne = Doctor::query()->updateOrCreate(
            ['license_number' => 'VET-LAN-001'],
            [
                'user_id' => $vetOneUser->id,
                'specialty' => 'General Practice',
                'phone' => $vetOneUser->phone,
                'email' => $vetOneUser->email,
                'years_of_experience' => 6,
                'is_active' => true,
            ],
        );

        $doctorTwo = Doctor::query()->updateOrCreate(
            ['license_number' => 'VET-MINH-001'],
            [
                'user_id' => $vetTwoUser->id,
                'specialty' => 'Internal Medicine',
                'phone' => $vetTwoUser->phone,
                'email' => $vetTwoUser->email,
                'years_of_experience' => 4,
                'is_active' => true,
            ],
        );

        $consultation = Service::query()->updateOrCreate(
            ['name' => 'General Consultation'],
            [
                'description' => 'Routine consultation and physical examination.',
                'price' => 150000,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
        );

        $labTest = Service::query()->updateOrCreate(
            ['name' => 'CBC Blood Test'],
            [
                'description' => 'Basic blood test package.',
                'price' => 250000,
                'duration_minutes' => 20,
                'is_active' => true,
            ],
        );

        $owner = User::query()->updateOrCreate(
            ['email' => 'owner.reception@example.com'],
            [
                'name' => 'Nguyen Thi Hoa',
                'phone' => '0902000001',
                'password' => 'password',
                'role_id' => $roles[Role::OWNER] ?? null,
            ],
        );

        $petOne = Pet::query()->updateOrCreate(
            [
                'owner_id' => $owner->id,
                'name' => 'Milo',
            ],
            [
                'species_id' => $dog->id,
                'breed' => 'Poodle',
                'gender' => 'male',
                'birth_date' => Carbon::today()->subYears(3),
                'weight' => 4.8,
                'color' => 'Apricot',
                'notes' => 'Friendly walk-in demo pet.',
            ],
        );

        $petTwo = Pet::query()->updateOrCreate(
            [
                'owner_id' => $owner->id,
                'name' => 'Luna',
            ],
            [
                'species_id' => $cat->id,
                'breed' => 'British Shorthair',
                'gender' => 'female',
                'birth_date' => Carbon::today()->subYears(2),
                'weight' => 3.5,
                'color' => 'Gray',
                'notes' => 'Cat patient for queue and billing demo.',
            ],
        );

        $today = Carbon::today();

        Appointment::query()->updateOrCreate(
            [
                'pet_id' => $petOne->id,
                'appointment_at' => $today->copy()->setTime(9, 0, 0),
            ],
            [
                'owner_id' => $owner->id,
                'doctor_id' => $doctorOne->id,
                'service_id' => $consultation->id,
                'status' => 'confirmed',
                'queue_number' => 1,
                'is_emergency' => false,
                'reason' => 'Vomiting and appetite loss',
            ],
        );

        Appointment::query()->updateOrCreate(
            [
                'pet_id' => $petTwo->id,
                'appointment_at' => $today->copy()->setTime(9, 20, 0),
            ],
            [
                'owner_id' => $owner->id,
                'doctor_id' => $doctorTwo->id,
                'service_id' => $consultation->id,
                'status' => 'confirmed',
                'queue_number' => 2,
                'is_emergency' => true,
                'reason' => 'Difficulty breathing',
            ],
        );

        Appointment::query()->updateOrCreate(
            [
                'pet_id' => $petOne->id,
                'appointment_at' => $today->copy()->setTime(10, 30, 0),
            ],
            [
                'owner_id' => $owner->id,
                'doctor_id' => $doctorOne->id,
                'service_id' => $labTest->id,
                'status' => 'pending',
                'queue_number' => null,
                'is_emergency' => false,
                'reason' => 'Follow-up blood test',
            ],
        );

        $completedAppointment = Appointment::query()->updateOrCreate(
            [
                'pet_id' => $petTwo->id,
                'appointment_at' => $today->copy()->subDay()->setTime(15, 0, 0),
            ],
            [
                'owner_id' => $owner->id,
                'doctor_id' => $doctorTwo->id,
                'service_id' => $labTest->id,
                'status' => 'completed',
                'queue_number' => 1,
                'is_emergency' => false,
                'reason' => 'Digestive discomfort',
            ],
        );

        MedicalRecord::query()->updateOrCreate(
            ['appointment_id' => $completedAppointment->id],
            [
                'pet_id' => $petTwo->id,
                'doctor_id' => $doctorTwo->id,
                'record_date' => $today->copy()->subDay(),
                'symptoms' => 'Loose stool and reduced appetite',
                'diagnosis' => 'Mild gastritis',
                'treatment' => 'GI support medication and hydration',
                'notes' => 'Demo unpaid billing record for receptionist screen.',
            ],
        );

        Payment::query()
            ->where('appointment_id', $completedAppointment->id)
            ->delete();
    }
}
