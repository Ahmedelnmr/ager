<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Unit;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ContractService
{
    public function __construct(
        protected RentScheduleService $scheduleService
    ) {}

    /**
     * Create a contract and auto-generate RentSchedules.
     */
    public function create(array $data): Contract
    {
        return DB::transaction(function () use ($data) {
            $contract = Contract::create($data);

            // Mark unit as rented
            Unit::where('id', $contract->unit_id)
                ->update(['status' => 'rented']);

            // Generate the full rent schedule
            $this->scheduleService->generateForContract($contract);

            $this->audit($contract, 'created');

            return $contract;
        });
    }

    /**
     * Update a contract (does NOT regenerate past schedules).
     */
    public function update(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            $old = $contract->toArray();
            $contract->update($data);
            $this->audit($contract, 'updated', $old);
            return $contract->fresh();
        });
    }

    /**
     * Terminate a contract with deposit handling.
     * $depositAction: 'keep' | 'refund' | 'partial'
     * $depositRefund: amount to refund if partial or full refund
     */
    public function terminate(Contract $contract, string $depositAction = 'keep', float $depositRefund = 0): Contract
    {
        return DB::transaction(function () use ($contract, $depositAction, $depositRefund) {
            $contract->update(['status' => 'terminated']);
            // Mark unit as vacant
            $contract->unit()->update(['status' => 'vacant']);
            $this->audit($contract, 'terminated', [
                'deposit_policy'  => $contract->deposit_policy,
                'deposit_amount'  => $contract->security_deposit_amount,
                'deposit_action'  => $depositAction,
                'deposit_refund'  => $depositRefund,
            ]);
            return $contract;
        });
    }

    private function audit(Contract $contract, string $action, array $old = []): void
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => "contract.{$action}",
            'model_type' => Contract::class,
            'model_id'   => $contract->id,
            'changes'    => $old ? array_diff_assoc($contract->toArray(), $old) : null,
            'ip_address' => Request::ip(),
        ]);
    }
}
