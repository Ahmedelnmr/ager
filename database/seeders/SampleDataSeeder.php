<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Contract;
use App\Services\ContractService;
use App\Services\RentScheduleService;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Buildings
        $b1 = Building::create([
            'name'    => 'برج النخيل',
            'address' => 'شارع الملك فهد',
            'city'    => 'الرياض',
            'notes'   => 'مبنى سكني متعدد الطوابق',
            'settings' => [
                'currency'              => 'ج.م',
                'late_penalty_type'     => 'percent',
                'late_penalty_value'    => 5,
                'annual_increase_default' => 5,
            ],
        ]);

        $b2 = Building::create([
            'name'    => 'برج التجارة',
            'address' => 'شارع العليا',
            'city'    => 'الرياض',
            'notes'   => 'مبنى تجاري',
            'settings' => [
                'currency'              => 'ج.م',
                'late_penalty_type'     => 'fixed',
                'late_penalty_value'    => 500,
                'annual_increase_default' => 3,
            ],
        ]);

        // Units
        $units = [
            ['building_id' => $b1->id, 'unit_number' => '101', 'floor' => '1', 'type' => 'residential', 'size' => 120, 'status' => 'vacant', 'base_rent' => 3000],
            ['building_id' => $b1->id, 'unit_number' => '102', 'floor' => '1', 'type' => 'residential', 'size' => 150, 'status' => 'vacant', 'base_rent' => 3500],
            ['building_id' => $b1->id, 'unit_number' => '201', 'floor' => '2', 'type' => 'residential', 'size' => 120, 'status' => 'vacant', 'base_rent' => 3200],
            ['building_id' => $b1->id, 'unit_number' => '202', 'floor' => '2', 'type' => 'residential', 'size' => 100, 'status' => 'vacant', 'base_rent' => 2800],
            ['building_id' => $b2->id, 'unit_number' => 'A01', 'floor' => 'G', 'type' => 'commercial',  'size' => 80,  'status' => 'vacant', 'base_rent' => 5000],
            ['building_id' => $b2->id, 'unit_number' => 'A02', 'floor' => 'G', 'type' => 'office',      'size' => 60,  'status' => 'vacant', 'base_rent' => 4000],
        ];
        $createdUnits = [];
        foreach ($units as $u) {
            $createdUnits[] = Unit::create($u);
        }

        // Tenants
        $tenants = [
            ['name' => 'أحمد محمد السالم', 'national_id' => '1234567890', 'phone' => '0501111111', 'email' => 'ahmed@example.com'],
            ['name' => 'فهد عبدالله القحطاني', 'national_id' => '1234567891', 'phone' => '0502222222', 'email' => 'fahad@example.com'],
            ['name' => 'شركة النجم للتجارة', 'national_id' => '1234567892', 'phone' => '0503333333', 'email' => 'najm@example.com'],
        ];
        $createdTenants = [];
        foreach ($tenants as $t) {
            $createdTenants[] = Tenant::create($t);
        }

        // Contracts with different configurations
        $scheduleService = new RentScheduleService();
        $contractService = new ContractService($scheduleService);

        $contractsData = [
            // Monthly with 5% annual increase and percent penalty
            [
                'unit_id'               => $createdUnits[0]->id,
                'tenant_id'             => $createdTenants[0]->id,
                'start_date'            => '2025-01-01',
                'end_date'              => '2026-12-31',
                'base_rent'             => 3000,
                'payment_cycle'         => 'monthly',
                'due_day'               => 5,
                'security_deposit_amount' => 3000,
                'deposit_policy'        => 'refundable',
                'annual_increase_type'  => 'percent',
                'annual_increase_value' => 5,
                'late_penalty_type'     => 'percent',
                'late_penalty_value'    => 5,
                'status'                => 'active',
            ],
            // Quarterly with fixed increase
            [
                'unit_id'               => $createdUnits[1]->id,
                'tenant_id'             => $createdTenants[1]->id,
                'start_date'            => '2025-04-01',
                'end_date'              => '2027-03-31',
                'base_rent'             => 10500,
                'payment_cycle'         => 'quarterly',
                'due_day'               => 1,
                'security_deposit_amount' => 10500,
                'deposit_policy'        => 'deduct_last_month',
                'annual_increase_type'  => 'fixed',
                'annual_increase_value' => 500,
                'late_penalty_type'     => 'fixed',
                'late_penalty_value'    => 500,
                'status'                => 'active',
            ],
            // Commercial yearly contract
            [
                'unit_id'               => $createdUnits[4]->id,
                'tenant_id'             => $createdTenants[2]->id,
                'start_date'            => '2024-01-01',
                'end_date'              => '2026-12-31',
                'base_rent'             => 60000,
                'payment_cycle'         => 'yearly',
                'due_day'               => 1,
                'security_deposit_amount' => 60000,
                'deposit_policy'        => 'non_refundable',
                'annual_increase_type'  => 'percent',
                'annual_increase_value' => 3,
                'late_penalty_type'     => 'fixed',
                'late_penalty_value'    => 2000,
                'status'                => 'active',
            ],
        ];

        foreach ($contractsData as $cd) {
            $contractService->create($cd);
        }
    }
}
