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
     * Throws \RuntimeException if the unit already has an active contract.
     */
    public function create(array $data): Contract
    {
        return DB::transaction(function () use ($data) {
            // ── Guard: prevent double-booking ──────────────────────────────
            $alreadyRented = Contract::where('unit_id', $data['unit_id'])
                ->where('status', 'active')
                ->exists();

            if ($alreadyRented) {
                $unit = Unit::find($data['unit_id']);
                throw new \RuntimeException(
                    "الوحدة «{$unit?->unit_number}» مؤجرة بالفعل بعقد نشط. "
                    . "يرجى إنهاء العقد الحالي أولاً قبل إنشاء عقد جديد."
                );
            }

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

    /**
     * Auto-expire contracts whose end_date has passed.
     * Sets contract status to 'expired' and unit status to 'vacant'.
     * Called daily by the scheduler.
     */
    public function autoExpireContracts(): int
    {
        $count = 0;
        $expired = Contract::where('status', 'active')
            ->where('end_date', '<', now()->startOfDay())
            ->with('unit')
            ->get();

        foreach ($expired as $contract) {
            DB::transaction(function () use ($contract) {
                $contract->update(['status' => 'expired']);
                $contract->unit?->update(['status' => 'vacant']);
                $this->audit($contract, 'auto_expired');
            });
            $count++;
        }

        return $count;
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
