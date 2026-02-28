<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\RentSchedule;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\ContractService;
use App\Services\RentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentRecordingTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private ContractService $contractService;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'owner']);
        $this->owner = User::factory()->create();
        $this->owner->assignRole('owner');
        $this->contractService = new ContractService(new RentScheduleService());
    }

    private function createContractWithSchedule(): RentSchedule
    {
        $building = Building::create(['name' => 'Building', 'settings' => []]);
        $unit     = Unit::create([
            'building_id' => $building->id, 'unit_number' => '101',
            'floor' => '1', 'type' => 'residential', 'status' => 'vacant', 'base_rent' => 2000,
        ]);
        $tenant = Tenant::create(['name' => 'Tenant', 'phone' => '0500000001']);

        $contract = $this->contractService->create([
            'unit_id' => $unit->id, 'tenant_id' => $tenant->id,
            'start_date' => '2025-01-01', 'end_date' => '2025-12-31',
            'base_rent' => 2000, 'payment_cycle' => 'monthly', 'due_day' => 1,
            'deposit_policy' => 'refundable', 'security_deposit_amount' => 0,
            'annual_increase_type' => 'none', 'annual_increase_value' => 0,
            'late_penalty_type' => 'none', 'late_penalty_value' => 0, 'status' => 'active',
        ]);

        return $contract->rentSchedules()->first();
    }

    public function test_can_record_full_payment()
    {
        $schedule = $this->createContractWithSchedule();

        $response = $this->actingAs($this->owner)->post(route('payments.store', $schedule), [
            'amount'         => $schedule->final_amount,
            'payment_method' => 'cash',
            'payment_date'   => '2025-01-05',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', ['amount' => $schedule->final_amount]);
        $this->assertEquals('paid', $schedule->fresh()->status);
    }

    public function test_partial_payment_sets_partial_status()
    {
        $schedule = $this->createContractWithSchedule();
        $partial  = $schedule->final_amount / 2;

        $this->actingAs($this->owner)->post(route('payments.store', $schedule), [
            'amount'         => $partial,
            'payment_method' => 'transfer',
            'payment_date'   => '2025-01-05',
        ]);

        $this->assertEquals('partial', $schedule->fresh()->status);
        $this->assertEquals($partial, $schedule->fresh()->paid_amount);
    }

    public function test_payment_amount_must_be_positive()
    {
        $schedule = $this->createContractWithSchedule();

        $response = $this->actingAs($this->owner)->post(route('payments.store', $schedule), [
            'amount'         => 0,
            'payment_method' => 'cash',
            'payment_date'   => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('amount');
    }
}
