<?php

namespace Tests\Unit;

use App\Models\Building;
use App\Models\Contract;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\ContractService;
use App\Services\RentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractServiceTest extends TestCase
{
    use RefreshDatabase;

    private ContractService $contractService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contractService = new ContractService(new RentScheduleService());
    }

    private function makeContract(array $overrides = []): array
    {
        $building = Building::create(['name' => 'Test Building', 'settings' => []]);
        $unit     = Unit::create([
            'building_id' => $building->id,
            'unit_number' => '101', 'floor' => '1',
            'type' => 'residential', 'status' => 'vacant', 'base_rent' => 1000,
        ]);
        $tenant = Tenant::create(['name' => 'Test Tenant', 'phone' => '0500000000']);

        return array_merge([
            'unit_id'               => $unit->id,
            'tenant_id'             => $tenant->id,
            'start_date'            => '2025-01-01',
            'end_date'              => '2025-12-31',
            'base_rent'             => 1000,
            'payment_cycle'         => 'monthly',
            'due_day'               => 1,
            'deposit_policy'        => 'refundable',
            'security_deposit_amount' => 1000,
            'annual_increase_type'  => 'none',
            'annual_increase_value' => 0,
            'late_penalty_type'     => 'none',
            'late_penalty_value'    => 0,
            'status'                => 'active',
        ], $overrides);
    }

    public function test_creates_contract_and_generates_monthly_schedules()
    {
        $contract = $this->contractService->create($this->makeContract(['payment_cycle' => 'monthly']));

        $this->assertDatabaseHas('contracts', ['id' => $contract->id]);
        // 12 months (Jan–Dec 2025)
        $this->assertCount(12, $contract->rentSchedules);
    }

    public function test_creates_contract_with_quarterly_schedules()
    {
        $contract = $this->contractService->create($this->makeContract(['payment_cycle' => 'quarterly']));
        $this->assertCount(4, $contract->rentSchedules);
    }

    public function test_creates_contract_with_yearly_schedule()
    {
        $contract = $this->contractService->create($this->makeContract([
            'payment_cycle' => 'yearly',
            'base_rent'     => 12000,
        ]));
        $this->assertCount(1, $contract->rentSchedules);
        $this->assertEquals(12000, $contract->rentSchedules->first()->final_amount);
    }

    public function test_applies_percent_annual_increase_on_second_year()
    {
        $contract = $this->contractService->create($this->makeContract([
            'start_date'            => '2025-01-01',
            'end_date'              => '2026-12-31',
            'payment_cycle'         => 'monthly',
            'base_rent'             => 1000,
            'annual_increase_type'  => 'percent',
            'annual_increase_value' => 10,
        ]));

        $schedules = $contract->rentSchedules->sortBy('due_date')->values();

        // First year (months 1-12): 1000
        $this->assertEquals(1000, $schedules->get(0)->base_amount);
        $this->assertEquals(1000, $schedules->get(11)->base_amount);
        // Second year (months 13-24): 1000 * 1.10 = 1100
        $this->assertEquals(1100, $schedules->get(12)->base_amount);
    }

    public function test_applies_fixed_annual_increase()
    {
        $contract = $this->contractService->create($this->makeContract([
            'start_date'            => '2025-01-01',
            'end_date'              => '2026-12-31',
            'payment_cycle'         => 'monthly',
            'base_rent'             => 1000,
            'annual_increase_type'  => 'fixed',
            'annual_increase_value' => 200,
        ]));

        $schedules = $contract->rentSchedules->sortBy('due_date')->values();
        // Second year: 1000 + 200 = 1200
        $this->assertEquals(1200, $schedules->get(12)->base_amount);
    }

    public function test_terminate_contract_updates_status()
    {
        $contract = $this->contractService->create($this->makeContract());
        $this->contractService->terminate($contract);

        $this->assertEquals('terminated', $contract->fresh()->status);
    }

    public function test_unit_status_changes_to_rented_after_contract()
    {
        $data     = $this->makeContract();
        $contract = $this->contractService->create($data);

        $this->assertEquals('rented', $contract->unit->fresh()->status);
    }
}
