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
        return [
            $contract->id,
            $contract->tenant->name,
            $contract->unit->building->name,
            $contract->unit->unit_number,
            $contract->start_date->format('Y-m-d'),
            $contract->end_date->format('Y-m-d'),
            $contract->base_rent,
            $contract->payment_cycle,
            $contract->status,
        ];
    }
}
