<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\RentSchedule;
use Carbon\Carbon;

class RentScheduleService
{
    /**
     * Generate all RentSchedule rows for a contract from start_date to end_date.
     */
    public function generateForContract(Contract $contract): void
    {
        // Delete existing future schedules (if regenerating)
        $contract->rentSchedules()
            ->where('due_date', '>=', now())
            ->whereIn('status', ['due', 'overdue'])
            ->delete();

        $periods  = $this->buildPeriods($contract);
        $baseRent = (float) $contract->base_rent;
        $yearlyIncreaseApplied = 0;

        foreach ($periods as $index => $period) {
            $yearNumber = (int) floor($index / $this->cyclePerYear($contract->payment_cycle));

            // Apply annual increase
            if ($yearNumber > 0 && $yearNumber > $yearlyIncreaseApplied) {
                $baseRent = $this->applyAnnualIncrease($contract, $baseRent);
                $yearlyIncreaseApplied = $yearNumber;
            }

            RentSchedule::create([
                'contract_id'    => $contract->id,
                'due_date'       => $period['due_date'],
                'period_label'   => $period['label'],
                'base_amount'    => $baseRent,
                'extra_charges'  => [],
                'penalty_amount' => 0,
                'discount_amount'=> 0,
                'final_amount'   => $baseRent,
                'paid_amount'    => 0,
                'status'         => 'due',
            ]);
        }
    }

    /**
     * Recalculate final_amount for a specific schedule.
     */
    public function recalculate(RentSchedule $schedule): void
    {
        $extra = collect($schedule->extra_charges ?? [])->sum('amount');
        $final = $schedule->base_amount
            + $extra
            + $schedule->penalty_amount
            - $schedule->discount_amount;

        $schedule->update(['final_amount' => max(0, $final)]);
    }

    // ─── Private helpers ───────────────────────────────────────────

    /**
     * Build array of ['due_date' => Carbon, 'label' => '2026-03'] periods.
     */
    private function buildPeriods(Contract $contract): array
    {
        $periods   = [];
        $current   = Carbon::parse($contract->start_date);
        $end       = Carbon::parse($contract->end_date);
        $dueDay    = (int) ($contract->due_day ?? $current->day);
        $interval  = $this->cycleInterval($contract->payment_cycle);

        while ($current->lte($end)) {
            $dueDate = $current->copy()->day(min($dueDay, $current->daysInMonth));
            $periods[] = [
                'due_date' => $dueDate,
                'label'    => $current->format('Y-m'),
            ];
            $current->add($interval);
        }

        return $periods;
    }

    private function cycleInterval(string $cycle): string
    {
        return match ($cycle) {
            'monthly'   => '1 month',
            'quarterly' => '3 months',
            'yearly'    => '1 year',
            default     => '1 month',
        };
    }

    private function cyclePerYear(string $cycle): int
    {
        return match ($cycle) {
            'monthly'   => 12,
            'quarterly' => 4,
            'yearly'    => 1,
            default     => 12,
        };
    }

    private function applyAnnualIncrease(Contract $contract, float $base): float
    {
        return match ($contract->annual_increase_type) {
            'percent' => $base * (1 + ($contract->annual_increase_value / 100)),
            'fixed'   => $base + $contract->annual_increase_value,
            default   => $base,
        };
    }
}
