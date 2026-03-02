<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\RentSchedule;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class PaymentService
{
    public function __construct(
        protected RentScheduleService $scheduleService
    ) {}

    /**
     * Record a payment against a rent schedule.
     */
    public function record(RentSchedule $schedule, array $data): Payment
    {
        return DB::transaction(function () use ($schedule, $data) {
            $payment = Payment::create([
                'rent_schedule_id' => $schedule->id,
                'contract_id'      => $schedule->contract_id,
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'payment_date'     => $data['payment_date'] ?? now(),
                'transaction_ref'  => $data['transaction_ref'] ?? null,
                'collected_by'     => Auth::id(),
                'notes'            => $data['notes'] ?? null,
            ]);

            // Update paid_amount and status on the schedule
            $newPaid = (float) $schedule->paid_amount + (float) $data['amount'];
            $final   = (float) $schedule->final_amount;

            $status = match(true) {
                $newPaid >= $final                   => 'paid',
                $schedule->status === 'overdue'       => 'overdue',  // keep overdue status until fully paid
                default                              => 'partial',
            };

            $schedule->update([
                'paid_amount' => $newPaid,
                'status'      => $status,
                'paid_at'     => $status === 'paid' ? now() : $schedule->paid_at,
                'receipt_number' => $data['receipt_number'] ?? ('RCP-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)),
            ]);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'payment.recorded',
                'model_type' => Payment::class,
                'model_id'   => $payment->id,
                'changes'    => ['amount' => $data['amount'], 'schedule_id' => $schedule->id],
                'ip_address' => Request::ip(),
            ]);

            return $payment;
        });
    }
}
