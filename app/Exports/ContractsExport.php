<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContractsExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(protected array $filters = []) {}

    public function title(): string { return 'العقود'; }

    public function query()
    {
        $query = Contract::with(['tenant', 'unit.building'])->latest();
        if (!empty($this->filters['status'])) $query->where('status', $this->filters['status']);
        return $query;
    }

    public function headings(): array
    {
        return ['رقم العقد', 'المستأجر', 'المبنى', 'الوحدة', 'تاريخ البداية', 'تاريخ النهاية', 'الإيجار الشهري', 'دورة الدفع', 'الحالة'];
    }

    public function map($contract): array
    {
        $statuses = [
            'active'     => 'نشط',
            'expired'    => 'منتهي',
            'terminated' => 'منهي مبكرًا',
        ];
        $cycles = [
            'monthly'   => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly'    => 'سنوي',
        ];
        return [
            $contract->id,
            $contract->tenant->name,
            $contract->unit->building->name,
            $contract->unit->unit_number,
            $contract->start_date->format('Y-m-d'),
            $contract->end_date->format('Y-m-d'),
            number_format($contract->base_rent, 2),
            $cycles[$contract->payment_cycle] ?? $contract->payment_cycle,
            $statuses[$contract->status] ?? $contract->status,
        ];
    }
}
