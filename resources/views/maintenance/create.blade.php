@extends('layouts.app')
@section('title', 'إضافة طلب صيانة')
@section('page-title', 'إضافة طلب صيانة')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-tools me-2 text-warning"></i>بيانات الطلب</div>
    <div class="card-body">
        <form method="POST" action="{{ route('maintenance.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">الوحدة <span class="text-danger">*</span></label>
                <select name="unit_id" class="form-select" required>
                    <option value="">اختر الوحدة...</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>{{ $u->building->name }} — {{ $u->unit_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    <option value="pending">معلق</option>
                    <option value="in_progress">جاري</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">اسم البالغ</label>
                <input type="text" name="reported_by" class="form-control" value="{{ old('reported_by') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">التكلفة (ريال)</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" step="0.01" min="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">وصف المشكلة <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>حفظ</button>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
