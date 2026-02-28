@extends('layouts.app')
@section('title', 'تعديل عقد')
@section('page-title', 'تعديل العقد #' . $contract->id)
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil-fill me-2 text-warning"></i>تعديل بيانات العقد</div>
    <div class="card-body">
        <form method="POST" action="{{ route('contracts.update', $contract) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">المستأجر</label>
                <select name="tenant_id" class="form-select" required>
                    @foreach($tenants as $t)
                    <option value="{{ $t->id }}" {{ $contract->tenant_id==$t->id?'selected':'' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الوحدة</label>
                <select name="unit_id" class="form-select" required>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ $contract->unit_id==$u->id?'selected':'' }}>{{ $u->building->name }} - {{ $u->unit_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">تاريخ البداية</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">تاريخ النهاية</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الإيجار الأساسي</label>
                <input type="number" name="base_rent" class="form-control" value="{{ old('base_rent', $contract->base_rent) }}" step="0.01">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">دورة الدفع</label>
                <select name="payment_cycle" class="form-select">
                    @foreach(['monthly'=>'شهري','quarterly'=>'ربع سنوي','yearly'=>'سنوي'] as $v=>$l)
                    <option value="{{ $v }}" {{ $contract->payment_cycle==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">يوم الاستحقاق</label>
                <input type="number" name="due_day" class="form-control" value="{{ old('due_day', $contract->due_day) }}" min="1" max="31">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">التأمين (ريال)</label>
                <input type="number" name="security_deposit_amount" class="form-control" value="{{ old('security_deposit_amount', $contract->security_deposit_amount) }}" step="0.01">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">سياسة التأمين</label>
                <select name="deposit_policy" class="form-select">
                    @foreach(['refundable'=>'قابل للاسترداد','deduct_last_month'=>'خصم من آخر شهر','non_refundable'=>'غير مسترد','partial'=>'جزئي'] as $v=>$l)
                    <option value="{{ $v }}" {{ $contract->deposit_policy==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">نوع الزيادة السنوية</label>
                <select name="annual_increase_type" class="form-select">
                    @foreach(['none'=>'لا يوجد','percent'=>'نسبة مئوية','fixed'=>'ثابت'] as $v=>$l)
                    <option value="{{ $v }}" {{ $contract->annual_increase_type==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">قيمة الزيادة</label>
                <input type="number" name="annual_increase_value" class="form-control" value="{{ old('annual_increase_value', $contract->annual_increase_value) }}" step="0.01">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">نوع غرامة التأخير</label>
                <select name="late_penalty_type" class="form-select">
                    @foreach(['none'=>'لا يوجد','percent'=>'نسبة مئوية','fixed'=>'ثابت'] as $v=>$l)
                    <option value="{{ $v }}" {{ $contract->late_penalty_type==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">قيمة الغرامة</label>
                <input type="number" name="late_penalty_value" class="form-control" value="{{ old('late_penalty_value', $contract->late_penalty_value) }}" step="0.01">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    @foreach(['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'] as $v=>$l)
                    <option value="{{ $v }}" {{ $contract->status==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملف العقد الجديد (اختياري)</label>
                <input type="file" name="contract_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $contract->notes) }}</textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>تحديث العقد</button>
            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
