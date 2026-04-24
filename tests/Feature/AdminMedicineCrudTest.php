<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminMedicineCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_medicine(): void
    {
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'slug' => Role::ADMIN,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Sanctum::actingAs($admin);

        $createResponse = $this->postJson('/api/admin/medicines', [
            'name' => 'Vitamin Paste',
            'sku' => 'MED-VIT-001',
            'unit' => 'tube',
            'stock_quantity' => 12,
            'price' => 89000,
            'description' => 'Nutrition support for recovery.',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Vitamin Paste')
            ->assertJsonPath('data.stock_quantity', 12);

        $medicineId = $createResponse->json('data.id');

        $this->putJson("/api/admin/medicines/{$medicineId}", [
            'name' => 'Vitamin Paste Plus',
            'sku' => 'MED-VIT-001',
            'unit' => 'tube',
            'stock_quantity' => 20,
            'price' => 99000,
            'description' => 'Updated nutrition support.',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Vitamin Paste Plus')
            ->assertJsonPath('data.stock_quantity', 20);

        $this->deleteJson("/api/admin/medicines/{$medicineId}")
            ->assertOk();

        $this->assertDatabaseMissing('medicines', [
            'id' => $medicineId,
        ]);
    }

    public function test_non_admin_role_cannot_access_admin_medicine_endpoints(): void
    {
        $adminRole = Role::query()->create([
            'name' => 'Admin',
            'slug' => Role::ADMIN,
        ]);

        $ownerRole = Role::query()->create([
            'name' => 'Owner',
            'slug' => Role::OWNER,
        ]);

        $owner = User::factory()->create([
            'role_id' => $ownerRole->id,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'Skin Care Spray',
            'sku' => 'MED-SKIN-001',
            'unit' => 'bottle',
            'stock_quantity' => 5,
            'price' => 160000,
        ]);

        Sanctum::actingAs($owner);

        $this->getJson('/api/admin/medicines')->assertForbidden();
        $this->postJson('/api/admin/medicines', [
            'name' => 'Blocked',
            'stock_quantity' => 1,
            'price' => 1000,
        ])->assertForbidden();
        $this->putJson("/api/admin/medicines/{$medicine->id}", [
            'name' => 'Blocked edit',
            'stock_quantity' => 1,
            'price' => 1000,
        ])->assertForbidden();
        $this->deleteJson("/api/admin/medicines/{$medicine->id}")->assertForbidden();
    }
}
