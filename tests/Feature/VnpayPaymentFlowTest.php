<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Service;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VnpayPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_can_start_and_complete_vnpay_payment_for_completed_appointment(): void
    {
        config([
            'vnpay.enabled' => true,
            'vnpay.tmn_code' => 'DEMOV210',
            'vnpay.hash_secret' => 'SECRETKEY123',
            'app.url' => 'http://localhost',
        ]);

        [$owner, $receptionist, $pet] = $this->createOwnerReceptionistAndPet();

        $service = Service::query()->create([
            'name' => 'General Checkup',
            'price' => 250000,
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'pet_id' => $pet->id,
            'owner_id' => $owner->id,
            'service_id' => $service->id,
            'appointment_at' => Carbon::now(),
            'status' => 'completed',
        ]);

        Sanctum::actingAs($receptionist);

        $response = $this->postJson('/api/receptionist/payments', [
            'appointment_id' => $appointment->id,
            'payment_method' => 'vnpay',
            'amount' => 250000,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $txnRef = $response->json('data.transaction_code');
        $this->assertNotEmpty($txnRef);
        $this->assertStringContainsString('https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', $response->json('payment_url'));

        $signedReturn = $this->signedReturnPayload($txnRef, 25000000);

        $returnResponse = $this->get(route('payments.vnpay.return', $signedReturn));
        $returnResponse->assertStatus(302);
        $this->assertStringContainsString('/receptionist/billing', (string) $returnResponse->headers->get('Location'));

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'payment_method' => 'vnpay',
            'gateway' => 'vnpay',
            'status' => 'paid',
            'gateway_transaction_no' => '14523467',
            'gateway_response_code' => '00',
        ]);
    }

    public function test_receptionist_can_start_and_complete_vnpay_payment_for_medicine_order(): void
    {
        config([
            'vnpay.enabled' => true,
            'vnpay.tmn_code' => 'DEMOV210',
            'vnpay.hash_secret' => 'SECRETKEY123',
            'app.url' => 'http://localhost',
        ]);

        [$owner, $receptionist, $pet] = $this->createOwnerReceptionistAndPet();

        $medicine = Medicine::query()->create([
            'name' => 'Skin Care Spray',
            'sku' => 'MED-SKN-050',
            'unit' => 'bottle',
            'stock_quantity' => 10,
            'price' => 160000,
        ]);

        $order = MedicineOrder::query()->create([
            'owner_id' => $owner->id,
            'pet_id' => $pet->id,
            'status' => 'pending',
            'total_amount' => 160000,
        ]);

        $order->items()->create([
            'medicine_id' => $medicine->id,
            'quantity' => 1,
            'unit_price' => 160000,
            'line_total' => 160000,
        ]);

        Sanctum::actingAs($receptionist);

        $this->patchJson("/api/receptionist/medicine-orders/{$order->id}/confirm", [
            'payment_method' => 'vnpay',
        ])->assertOk()
            ->assertJsonPath('data.status', 'confirmed');

        $collectResponse = $this->patchJson("/api/receptionist/medicine-orders/{$order->id}/collect-payment", [
            'payment_method' => 'vnpay',
        ]);

        $collectResponse->assertOk()
            ->assertJsonPath('data.payment.status', 'pending');

        $txnRef = $collectResponse->json('data.payment.transaction_code');
        $this->assertNotEmpty($txnRef);

        $signedReturn = $this->signedReturnPayload($txnRef, 16000000);

        $returnResponse = $this->get(route('payments.vnpay.return', $signedReturn));
        $returnResponse->assertStatus(302);
        $this->assertStringContainsString('/receptionist/billing', (string) $returnResponse->headers->get('Location'));

        $this->assertDatabaseHas('payments', [
            'medicine_order_id' => $order->id,
            'payment_method' => 'vnpay',
            'gateway' => 'vnpay',
            'status' => 'paid',
            'gateway_transaction_no' => '14523467',
        ]);

        $this->assertDatabaseHas('medicine_orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: Pet}
     */
    private function createOwnerReceptionistAndPet(): array
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

        return [$owner, $receptionist, $pet];
    }

    /**
     * @return array<string, string>
     */
    private function signedReturnPayload(string $txnRef, int $amount): array
    {
        $params = [
            'vnp_Amount' => (string) $amount,
            'vnp_BankCode' => 'NCB',
            'vnp_BankTranNo' => 'VNP14226112',
            'vnp_CardType' => 'ATM',
            'vnp_OrderInfo' => 'Thanh toan thu nghiem',
            'vnp_PayDate' => '20260427120000',
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => 'DEMOV210',
            'vnp_TransactionNo' => '14523467',
            'vnp_TransactionStatus' => '00',
            'vnp_TxnRef' => $txnRef,
        ];

        ksort($params);

        $query = collect($params)
            ->map(fn ($value, $key) => urlencode($key) . '=' . urlencode($value))
            ->implode('&');

        $params['vnp_SecureHash'] = hash_hmac('sha512', $query, 'SECRETKEY123');

        return $params;
    }
}
