<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\RentSchedule;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendDailyRentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected NotificationService $notificationService) {}

    public function handle(): void
    {
        $today = Carbon::today();

        // 1. Notify tenants whose rent is due TODAY
        $dueToday = RentSchedule::with(['contract.tenant', 'contract.unit.building'])
            ->whereDate('due_date', $today)
            ->whereIn('status', ['due', 'partial'])
            ->get();

        foreach ($dueToday as $schedule) {
            $contract = $schedule->contract;
            if (!$contract || !$contract->tenant) continue;

            // Notify all users who manage this building
            $this->notifyOwners(
                title: "📅 استحقاق إيجار اليوم",
                body:  "المستأجر {$contract->tenant->name} — {$contract->unit->building->name} وحدة {$contract->unit->unit_number} — مبلغ " . number_format($schedule->final_amount - $schedule->paid_amount) . " ج.م مستحق الدفع اليوم.",
                url:   route('payments.create', $schedule)
            );
        }

        // 2. Notify about overdue rent schedules (reminder every day)
        $overdue = RentSchedule::with(['contract.tenant', 'contract.unit.building'])
            ->where('status', 'overdue')
            ->whereDate('due_date', '>=', $today->copy()->subDays(60)) // last 60 days only
            ->get();

        foreach ($overdue as $schedule) {
            $contract = $schedule->contract;
            if (!$contract || !$contract->tenant) continue;

            $daysLate = $today->diffInDays(Carbon::parse($schedule->due_date));

            $this->notifyOwners(
                title: "⚠️ إيجار متأخر — {$daysLate} يوم",
                body:  "المستأجر {$contract->tenant->name} — وحدة {$contract->unit->unit_number} — متأخر {$daysLate} يوم — مبلغ " . number_format($schedule->final_amount - $schedule->paid_amount) . " ج.م",
                url:   route('rent-schedules.show', $schedule)
            );
        }
    }

    private function notifyOwners(string $title, string $body, string $url): void
    {
        // Get all users with owner or admin role
        $users = \App\Models\User::role(['owner', 'admin', 'accountant'])->get();
        foreach ($users as $user) {
            $this->notificationService->send($user->id, $title, $body, $url);
        }
    }
}
