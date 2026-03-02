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

        // ── 1. Rent DUE TODAY ─────────────────────────
        $dueToday = RentSchedule::with(['contract.tenant', 'contract.unit.building'])
            ->whereDate('due_date', $today)
            ->whereIn('status', ['due', 'partial'])
            ->get()
            ->filter(fn($s) => $s->contract?->tenant !== null);   // safe filter

        if ($dueToday->count() > 0) {
            $names = $dueToday->map(fn($s) =>
                ($s->contract->tenant->name) .
                ' (' . number_format((float)$s->final_amount - (float)$s->paid_amount) . ' ج.م)'
            )->implode('، ');

            $svc->notifyRoles(['owner','admin','accountant'], 'custom', [
                'title'   => "📅 استحقاق إيجار اليوم — {$dueToday->count()} مستأجر",
                'message' => $names,
                'count'   => $dueToday->count(),
                'url'     => url('/rent-schedules?status=due'),
            ]);
        }

        // ── 2. OVERDUE (last 90 days) ─────────────────
        $overdue = RentSchedule::with(['contract.tenant'])
            ->where('status', 'overdue')
            ->whereDate('due_date', '>=', $today->copy()->subDays(90))
            ->get()
            ->filter(fn($s) => $s->contract?->tenant !== null);   // safe filter

        if ($overdue->count() > 0) {
            $byTenant = $overdue->groupBy(fn($s) => $s->contract->tenant->name);

            $lines = $byTenant->map(fn($schedules, $name) =>
                $name . ': ' . number_format(
                    $schedules->sum(fn($s) => (float)$s->final_amount - (float)$s->paid_amount)
                ) . ' ج.م'
            )->implode('، ');

            $totalAmount = $overdue->sum(fn($s) => (float)$s->final_amount - (float)$s->paid_amount);

            $svc->notifyRoles(['owner','admin','accountant'], 'overdue_payment', [
                'title'   => "⚠️ إيجارات متأخرة — {$byTenant->count()} مستأجر — " . number_format($totalAmount) . ' ج.م',
                'message' => $lines,
                'count'   => $overdue->count(),
                'url'     => url('/rent-schedules?status=overdue'),
            ]);
        }
    }
}
