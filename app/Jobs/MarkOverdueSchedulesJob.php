<?php

namespace App\Jobs;

use App\Services\LatePenaltyService;
use App\Services\NotificationService;
use App\Models\RentSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkOverdueSchedulesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        LatePenaltyService  $penaltyService,
        NotificationService $notificationService
    ): void {
        $count = $penaltyService->processOverdue();

        if ($count > 0) {
            // Notify all owners/admins about overdue collection
            $overdueTotal = RentSchedule::where('status', 'overdue')
                ->selectRaw('SUM(final_amount - paid_amount) as total')
                ->value('total') ?? 0;

            $notificationService->notifyRoles(['owner', 'admin'], 'overdue_payment', [
                'count'        => $count,
                'total'        => $overdueTotal,
                'link'         => route('rent-schedules.index', ['status' => 'overdue']),
                'message'      => "يوجد {$count} استحقاق متأخر بإجمالي " . number_format($overdueTotal, 2) . " ريال.",
                'sms_message'  => "تنبيه: يوجد {$count} إيجار متأخر بإجمالي " . number_format($overdueTotal, 2) . " ريال.",
            ]);
        }
    }
}
