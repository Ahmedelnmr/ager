<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected array $filters = []) {}

    public function title(): string { return 'المدفوعات'; }

    public function query()
    {
        $query = Payment::with(['contract.tenant', 'contract.unit.building', 'rentSchedule'])
            ->latest('payment_date');
        if (!empty($this->filters['from'])) $query->where('payment_date', '>=', $this->filters['from']);
        if (!empty($this->filters['to']))   $query->where('payment_date', '<=', $this->filters['to']);
        return $query;
    }

    public function headings(): array
    {
        return ['رقم الإيصال', 'المستأجر', 'المبنى', 'الوحدة', 'الفترة', 'المبلغ', 'طريقة الدفع', 'تاريخ الدفع'];
    }

    public function map($payment): array
    {
        $methods = [
            'cash'          => 'نقدًا',
            'bank_transfer' => 'تحويل بنكي',
            'check'         => 'شيك',
            'online'        => 'أونلاين',
        ];
        return [
            $payment->rentSchedule?->receipt_number ?? $payment->id,
            $payment->contract->tenant->name,
            $payment->contract->unit->building->name,
            $payment->contract->unit->unit_number,
            $payment->rentSchedule?->period_label,
            number_format($payment->amount, 2),
            $methods[$payment->payment_method] ?? $payment->payment_method,
            $payment->payment_date->format('Y-m-d'),
        ];
    }
}
