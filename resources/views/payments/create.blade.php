@extends('layouts.app')
@section('title', 'استلام دفعة')
@section('page-title', 'تسجيل دفعة — الفترة: ' . $schedule->period_label)
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">

<div class="card mb-3 p-3 bg-light border-0">
    <div class="row g-2">
        <div class="col-6"><span class="text-muted small">المستأجر:</span> <strong>{{ $schedule->contract->tenant->name }}</strong></div>
        <div class="col-6"><span class="text-muted small">الوحدة:</span> <strong>{{ $schedule->contract->unit->building->name }} / {{ $schedule->contract->unit->unit_number }}</strong></div>
        <div class="col-6"><span class="text-muted small">الإجمالي المطلوب:</span> <strong class="text-primary">{{ number_format($schedule->final_amount) }} ريال</strong></div>
        <div class="col-6"><span class="text-muted small">المتبقي:</span> <strong class="text-danger">{{ number_format($schedule->remaining_amount) }} ريال</strong></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-cash-coin me-2 text-success"></i>استلام دفعة</div>
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store', $schedule) }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">المبلغ (ريال) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $schedule->remaining_amount) }}" step="0.01" min="0.01" max="{{ $schedule->remaining_amount }}" required>
                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">طريقة الدفع</label>
                <select name="payment_method" class="form-select">
                    <option value="cash">نقد</option>
                    <option value="transfer">تحويل بنكي</option>
                    <option value="cheque">شيك</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">تاريخ الدفع</label>
                <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">رقم المرجع / الشيك</label>
                <input type="text" name="transaction_ref" class="form-control" value="{{ old('transaction_ref') }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg me-1"></i>تسجيل الدفعة وطباعة الإيصال</button>
            <a href="{{ route('contracts.show', $schedule->contract) }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
