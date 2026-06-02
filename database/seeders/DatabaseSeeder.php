<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SpeciesSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(ReceptionistDemoSeeder::class);

        $roles = Role::query()->pluck('id', 'slug');

        $accounts = [
            [
                'name' => 'Owner Demo',
                'email' => 'owner@example.com',
                'password' => 'password',
                'role_slug' => Role::OWNER,
            ],
            [
                'name' => 'Vet Demo',
                'email' => 'vet@example.com',
                'password' => 'password',
                'role_slug' => Role::VET,
            ],
            [
                'name' => 'Receptionist Demo',
                'email' => 'receptionist@example.com',
                'password' => 'password',
                'role_slug' => Role::RECEPTIONIST,
            ],
            [
                'name' => 'Admin Demo',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role_slug' => Role::ADMIN,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate([
                'email' => $account['email'],
            ], [
                'name' => $account['name'],
                'password' => $account['password'],
                'role_id' => $roles[$account['role_slug']] ?? null,
            ]);
        }

        $vetUser = User::query()->where('email', 'vet@example.com')->first();

        if ($vetUser) {
            Doctor::query()->updateOrCreate(
                ['user_id' => $vetUser->id],
                [
                    'license_number' => 'VET-DEMO-001',
                    'specialty' => 'General Practice',
                    'phone' => $vetUser->phone,
                    'email' => $vetUser->email,
                    'years_of_experience' => 3,
                    'is_active' => true,
                ],
            );
        }
    }
}
