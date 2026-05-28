<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AiUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aiRole = Role::where('slug', Role::AI_ASSISTANT)->first();

        if ($aiRole) {
            User::updateOrCreate(
                ['email' => 'ai@petcare.local'],
                [
                    'name' => 'Trợ lý AI PetCare',
                    'password' => Hash::make('password123'),
                    'role_id' => $aiRole->id,
                    'phone' => '0000000000',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
