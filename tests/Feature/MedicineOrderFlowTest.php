<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MedicineOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_medicine_order_and_receptionist_can_confirm_and_collect_payment(): void
    {
        $ownerRole = Role::query()->create([
            'name' => 'Pet Owner',
            'slug' => Role::OWNER,
        ]);

        $receptionistRole = Role::query()->create([
            'name' => 'Receptionist',
            'slug' => Role::RECEPTIONIST,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $receptionist = User::factory()->create([
            'role_id' => $receptionistRole->id,
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

        $medicine = Medicine::query()->create([
            'name' => 'Amoxicillin 250mg',
            'sku' => 'MED-AMX-250',
            'unit' => 'box',
            'stock_quantity' => 10,
            'price' => 120000,
        ]);

        Sanctum::actingAs($owner);

        $createResponse = $this->postJson('/api/owner/medicine-orders', [
            'pet_id' => $pet->id,
            'notes' => 'Need medicine today',
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total_amount', '240000.00');

        $orderId = $createResponse->json('data.id');

        Sanctum::actingAs($receptionist);

        $this->patchJson("/api/receptionist/medicine-orders/{$orderId}/confirm", [
            'payment_method' => 'cash',
        ])->assertOk()
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.payment.status', 'pending')
            ->assertJsonPath('data.payment.amount', 240000);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stock_quantity' => 8,
        ]);

        $this->patchJson("/api/receptionist/medicine-orders/{$orderId}/collect-payment", [
            'payment_method' => 'cash',
        ])->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.payment.status', 'paid');

        $this->assertDatabaseHas('payments', [
            'medicine_order_id' => $orderId,
            'status' => 'paid',
            'payment_method' => 'cash',
        ]);
    }
}
