@extends('layouts.app')
@section('title', 'استحقاق ' . $rentSchedule->period_label)
@section('page-title', 'استحقاق الفترة: ' . $rentSchedule->period_label)
@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('rent-schedules.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    @if($rentSchedule->status !== 'paid')
    <a href="{{ route('payments.create', $rentSchedule) }}" class="btn btn-success"><i class="bi bi-cash me-1"></i>استلام دفعة</a>
    @endif
</div>
<div class="row g-3">
    <div class="col-md-5">
        <div class="card p-3">
            <h6 class="fw-bold border-bottom pb-2">تفاصيل الاستحقاق</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted">المستأجر</td><td><strong>{{ $rentSchedule->contract->tenant->name }}</strong></td></tr>
                <tr><td class="text-muted">الوحدة</td><td>{{ $rentSchedule->contract->unit->building->name }} / {{ $rentSchedule->contract->unit->unit_number }}</td></tr>
                <tr><td class="text-muted">تاريخ الاستحقاق</td><td>{{ $rentSchedule->due_date->format('Y-m-d') }}</td></tr>
                <tr><td class="text-muted">المبلغ الأساسي</td><td>{{ number_format($rentSchedule->base_amount) }} ريال</td></tr>
                <tr><td class="text-muted">الغرامة</td><td class="text-danger">{{ number_format($rentSchedule->penalty_amount) }} ريال</td></tr>
                <tr><td class="text-muted">الخصم</td><td class="text-success">{{ number_format($rentSchedule->discount_amount) }} ريال</td></tr>
                <tr><td class="text-muted">الإجمالي</td><td><strong class="text-primary">{{ number_format($rentSchedule->final_amount) }} ريال</strong></td></tr>
                <tr><td class="text-muted">المدفوع</td><td class="text-success">{{ number_format($rentSchedule->paid_amount) }} ريال</td></tr>
                <tr><td class="text-muted">المتبقي</td><td class="{{ $rentSchedule->remaining_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">{{ number_format($rentSchedule->remaining_amount) }} ريال</td></tr>
                <tr><td class="text-muted">الحالة</td><td><span class="badge badge-{{ $rentSchedule->status }} px-2 rounded-pill">{{ ['due'=>'مستحق','paid'=>'مدفوع','partial'=>'جزئي','overdue'=>'متأخر'][$rentSchedule->status] }}</span></td></tr>
            </table>
            <!-- Discount adjustment form -->
            <hr>
            <form method="POST" action="{{ route('rent-schedules.update', $rentSchedule) }}">
                @csrf @method('PATCH')
                <label class="form-label small fw-semibold">تعديل الخصم (ريال)</label>
                <div class="input-group input-group-sm">
                    <input type="number" name="discount_amount" class="form-control" value="{{ $rentSchedule->discount_amount }}" step="0.01" min="0">
                    <button class="btn btn-outline-warning">تطبيق</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">المدفوعات المسجلة</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>المبلغ</th><th>طريقة الدفع</th><th>التاريخ</th><th>بواسطة</th><th></th></tr></thead>
                    <tbody>
                        @forelse($rentSchedule->payments as $p)
                        <tr>
                            <td class="text-success fw-semibold">{{ number_format($p->amount) }}</td>
                            <td>{{ ['cash'=>'نقد','transfer'=>'تحويل','cheque'=>'شيك'][$p->payment_method] }}</td>
                            <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                            <td>{{ $p->collectedBy?->name ?? '—' }}</td>
                            <td><a href="{{ route('payments.download-receipt', $p) }}" class="btn btn-xs btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-file-pdf"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد مدفوعات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
