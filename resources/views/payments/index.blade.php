@extends('layouts.app')
@section('title', 'المدفوعات')
@section('page-title', 'سجل المدفوعات')
@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>رقم الإيصال</th><th>المستأجر</th><th>الوحدة</th><th>الفترة</th><th>المبلغ</th><th>طريقة الدفع</th><th>تاريخ الدفع</th><th>بواسطة</th><th></th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td>{{ $p->rentSchedule?->receipt_number ?? '#'.$p->id }}</td>
                    <td><a href="{{ route('tenants.show', $p->contract->tenant) }}" class="text-decoration-none">{{ $p->contract->tenant->name }}</a></td>
                    <td>{{ $p->contract->unit->building->name }} / {{ $p->contract->unit->unit_number }}</td>
                    <td>{{ $p->rentSchedule?->period_label ?? '—' }}</td>
                    <td class="fw-semibold text-success">{{ number_format($p->amount) }}</td>
                    <td>{{ ['cash'=>'نقد','transfer'=>'تحويل','cheque'=>'شيك'][$p->payment_method] }}</td>
                    <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                    <td>{{ $p->collectedBy?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('payments.receipt', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt"></i></a>
                        <a href="{{ route('payments.download-receipt', $p) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد مدفوعات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
@endsection
