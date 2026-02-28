<?php

namespace Tests\Unit;

use App\Models\Building;
use App\Models\Contract;
use App\Models\RentSchedule;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\ContractService;
use App\Services\LatePenaltyService;
use App\Services\RentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatePenaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    private LatePenaltyService $penaltyService;
    private ContractService $contractService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->penaltyService  = new LatePenaltyService();
        $this->contractService = new ContractService(new RentScheduleService());
    }

    private function makeContractWithPenalty(string $type, float $value): Contract
    {
        $building = Building::create(['name' => 'Test Building', 'settings' => []]);
        $unit     = Unit::create([
            'building_id' => $building->id, 'unit_number' => '101',
            'floor' => '1', 'type' => 'residential', 'status' => 'vacant', 'base_rent' => 1000,
        ]);
        $tenant = Tenant::create(['name' => 'Tenant', 'phone' => '0500000001']);

        return $this->contractService->create([
            'unit_id'               => $unit->id,
            'tenant_id'             => $tenant->id,
            'start_date'            => now()->subMonths(2)->startOfMonth()->toDateString(),
            'end_date'              => now()->addMonths(10)->endOfMonth()->toDateString(),
            'base_rent'             => 1000,
            'payment_cycle'         => 'monthly',
            'due_day'               => 1,
            'deposit_policy'        => 'refundable',
            'security_deposit_amount' => 0,
            'annual_increase_type'  => 'none',
            'annual_increase_value' => 0,
            'late_penalty_type'     => $type,
            'late_penalty_value'    => $value,
            'status'                => 'active',
        ]);
    }

    public function test_marks_overdue_schedules()
    {
        $contract = $this->makeContractWithPenalty('none', 0);

        // Force a schedule to be overdue
        $schedule = $contract->rentSchedules()->first();
        $schedule->update(['due_date' => now()->subDays(10), 'status' => 'due']);

        $count = $this->penaltyService->processOverdue();
        $this->assertGreaterThan(0, $count);
        $this->assertEquals('overdue', $schedule->fresh()->status);
    }

    public function test_applies_percent_penalty()
    {
        $contract = $this->makeContractWithPenalty('percent', 5);

        $schedule = $contract->rentSchedules()->first();
        $schedule->update(['due_date' => now()->subDays(5), 'status' => 'due', 'base_amount' => 1000, 'final_amount' => 1000]);

        $this->penaltyService->processOverdue();

        $updated = $schedule->fresh();
        // 5% of 1000 = 50
        $this->assertEquals(50, $updated->penalty_amount);
        $this->assertEquals(1050, $updated->final_amount);
    }

    public function test_applies_fixed_penalty()
    {
        $contract = $this->makeContractWithPenalty('fixed', 200);

        $schedule = $contract->rentSchedules()->first();
        $schedule->update(['due_date' => now()->subDays(5), 'status' => 'due', 'base_amount' => 1000, 'final_amount' => 1000]);

        $this->penaltyService->processOverdue();

        $updated = $schedule->fresh();
        $this->assertEquals(200, $updated->penalty_amount);
        $this->assertEquals(1200, $updated->final_amount);
    }

    public function test_no_penalty_when_type_is_none()
    {
        $contract = $this->makeContractWithPenalty('none', 0);

        $schedule = $contract->rentSchedules()->first();
        $schedule->update(['due_date' => now()->subDays(5), 'status' => 'due']);

        $this->penaltyService->processOverdue();

        $this->assertEquals(0, $schedule->fresh()->penalty_amount);
        $this->assertEquals('overdue', $schedule->fresh()->status);
    }
}
