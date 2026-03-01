<?php

namespace App\Jobs;

use App\Models\RentSchedule;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyRentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(NotificationService $svc): void
    {
        $today = Carbon::today();

        // ── 1. Rent DUE TODAY ──────────────────────────────────────────────
        $dueToday = RentSchedule::with(['contract.tenant', 'contract.unit.building'])
            ->whereDate('due_date', $today)
            ->whereIn('status', ['due', 'partial'])
            ->get();

        if ($dueToday->count() > 0) {
            $names = $dueToday->map(fn($s) =>
                ($s->contract->tenant->name ?? '؟') .
                ' (' . number_format($s->final_amount - $s->paid_amount) . ' ج.م)'
            )->implode('، ');

            $title = "📅 استحقاق إيجار اليوم — {$dueToday->count()} مستأجر";
            $body  = $names;

            $svc->notifyRoles(['owner','admin','accountant'], 'rent_due_today', [
                'title'   => $title,
                'message' => $body,
                'count'   => $dueToday->count(),
                'url'     => route('rent-schedules.index', ['status'=>'due']),
            ]);
        }

        // ── 2. OVERDUE rent (last 90 days) ─────────────────────────────────
        $overdue = RentSchedule::with(['contract.tenant', 'contract.unit.building'])
            ->where('status', 'overdue')
            ->whereDate('due_date', '>=', $today->copy()->subDays(90))
            ->get();

        if ($overdue->count() > 0) {
            // Group by tenant
            $byTenant = $overdue->groupBy(fn($s) => $s->contract->tenant->name ?? '؟');

            $lines = $byTenant->map(fn($schedules, $name) =>
                $name . ': ' . number_format($schedules->sum(fn($s) => $s->final_amount - $s->paid_amount)) . ' ج.م'
            )->implode('، ');

            $totalAmount = $overdue->sum(fn($s) => $s->final_amount - $s->paid_amount);
            $title = "⚠️ إيجارات متأخرة — {$byTenant->count()} مستأجر — " . number_format($totalAmount) . ' ج.م';

            $svc->notifyRoles(['owner','admin','accountant'], 'rent_overdue_reminder', [
                'title'   => $title,
                'message' => $lines,
                'count'   => $overdue->count(),
                'url'     => route('rent-schedules.index', ['status'=>'overdue']),
            ]);
        }
    }
}
