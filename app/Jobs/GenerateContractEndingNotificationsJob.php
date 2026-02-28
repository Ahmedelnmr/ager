<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateContractEndingNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService): void
    {
        $days = (int) config('rental.contract_ending_notify_days', 30);

        $contracts = Contract::endingSoon($days)
            ->with(['tenant', 'unit.building'])
            ->get();

        foreach ($contracts as $contract) {
            $daysLeft = now()->diffInDays($contract->end_date, false);
            $notificationService->notifyRoles(['owner', 'admin', 'accountant'], 'contract_ending', [
                'contract_id'    => $contract->id,
                'tenant_name'    => $contract->tenant->name,
                'unit'           => $contract->unit->unit_number,
                'building'       => $contract->unit->building->name,
                'end_date'       => $contract->end_date->format('Y-m-d'),
                'days_remaining' => $daysLeft,
                'link'           => route('contracts.show', $contract->id),
                'sms_message'    => "عقد المستأجر {$contract->tenant->name} ينتهي خلال {$daysLeft} يوماً.",
            ]);
        }
    }
}
