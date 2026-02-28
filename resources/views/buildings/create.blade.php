@extends('layouts.app')
@section('title', 'إضافة مبنى')
@section('page-title', 'إضافة مبنى جديد')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header"><i class="bi bi-building-fill me-2 text-primary"></i>بيانات المبنى</div>
    <div class="card-body">
        <form method="POST" action="{{ route('buildings.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">اسم المبنى <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">المدينة</label>
                <input type="text" name="city" class="form-control" value="{{ old('city') }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">العنوان</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-gear me-2"></i>الإعدادات الافتراضية</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">نوع غرامة التأخير</label>
                <select name="settings[late_penalty_type]" class="form-select">
                    <option value="none">لا توجد</option>
                    <option value="percent">نسبة مئوية</option>
                    <option value="fixed">مبلغ ثابت</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">قيمة الغرامة</label>
                <input type="number" name="settings[late_penalty_value]" class="form-control" value="0" step="0.01" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الزيادة السنوية الافتراضية (%)</label>
                <input type="number" name="settings[annual_increase_default]" class="form-control" value="0" step="0.01" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">العملة</label>
                <input type="text" name="settings[currency]" class="form-control" value="ريال">
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>حفظ</button>
            <a href="{{ route('buildings.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
