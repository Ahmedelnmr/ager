<?php

namespace App\Services;

use App\Models\RentSchedule;
use App\Models\Contract;
use Carbon\Carbon;

class LatePenaltyService
{
    /**
     * Mark overdue schedules and apply penalties.
     * Called daily by the scheduler.
     */
    public function processOverdue(): int
    {
        $count = 0;
        $overdueSchedules = RentSchedule::with('contract')
            ->whereIn('status', ['due', 'partial'])
            ->where('due_date', '<', now()->startOfDay())
            ->get();

        foreach ($overdueSchedules as $schedule) {
            $this->applyPenalty($schedule);
            $count++;
        }
        return $count;
    }

    /**
     * Apply penalty to a single schedule.
     */
    public function applyPenalty(RentSchedule $schedule): void
    {
        /** @var Contract|null $contract */
        $contract = $schedule->contract;
        if (!$contract) {
            return;
        }
        $penalty  = $this->calculatePenalty($contract, (float) $schedule->base_amount);

        $schedule->update([
            'status'         => 'overdue',
            'penalty_amount' => $penalty,
            'final_amount'   => $schedule->base_amount
                + collect($schedule->extra_charges ?? [])->sum('amount')
                + $penalty
                - $schedule->discount_amount,
        ]);
    }

    /**
     * Calculate penalty amount for a contract/amount.
     */
    public function calculatePenalty(Contract $contract, float $amount): float
    {
        $type  = $contract->late_penalty_type ?? 'none';
        $value = (float) ($contract->late_penalty_value ?? 0);

        return match ($type) {
            'percent' => round($amount * $value / 100, 2),
            'fixed'   => $value,
            default   => 0.0,
        };
    }
}
