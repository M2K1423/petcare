<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database with base roles.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Pet Owner',
                'slug' => Role::OWNER,
                'description' => 'Owns pets and books appointments.',
            ],
            [
                'name' => 'Vet',
                'slug' => Role::VET,
                'description' => 'Examines pets and updates medical records.',
            ],
            [
                'name' => 'Receptionist',
                'slug' => Role::RECEPTIONIST,
                'description' => 'Manages appointments, check-ins, and payments.',
            ],
            [
                'name' => 'Admin',
                'slug' => Role::ADMIN,
                'description' => 'Manages users, doctors, and services.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role,
            );
        }
    }
}
