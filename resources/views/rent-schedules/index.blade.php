@extends('layouts.app')
@section('title', 'جدول الاستحقاقات')
@section('page-title', 'جدول استحقاقات الإيجار')
@section('content')
<div class="card mb-3 p-3">
<form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">كل الحالات</option>
            @foreach(['due'=>'مستحق','paid'=>'مدفوع','partial'=>'جزئي','overdue'=>'متأخر'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}" placeholder="الشهر">
    </div>
    <div class="col-md-2"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i></button></div>
</form>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>الفترة</th><th>المستأجر</th><th>الوحدة</th><th>تاريخ الاستحقاق</th><th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th><th>الحالة</th><th>الإجراءات</th></tr></thead>
            <tbody>
                @forelse($schedules as $s)
                <tr class="{{ $s->status === 'overdue' ? 'table-danger' : '' }}">
                    <td>{{ $s->period_label }}</td>
                    <td>{{ $s->contract->tenant->name }}</td>
                    <td>{{ $s->contract->unit->building->name }} / {{ $s->contract->unit->unit_number }}</td>
                    <td>{{ $s->due_date->format('Y-m-d') }}</td>
                    <td>{{ number_format($s->final_amount) }}</td>
                    <td class="text-success">{{ number_format($s->paid_amount) }}</td>
                    <td class="{{ $s->remaining_amount > 0 ? 'text-danger fw-semibold' : 'text-success' }}">{{ number_format($s->remaining_amount) }}</td>
                    <td><span class="badge badge-{{ $s->status }} px-2 rounded-pill">{{ ['due'=>'مستحق','paid'=>'مدفوع','partial'=>'جزئي','overdue'=>'متأخر'][$s->status] }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($s->status !== 'paid')
                            <a href="{{ route('payments.create', $s) }}" class="btn btn-sm btn-success py-0 px-2" title="استلام دفعة"><i class="bi bi-cash"></i></a>
                            @endif
                            <a href="{{ route('rent-schedules.show', $s) }}" class="btn btn-sm btn-outline-primary py-0 px-2"><i class="bi bi-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد استحقاقات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $schedules->links() }}</div>
@endsection
