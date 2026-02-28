@extends('layouts.app')
@section('title', 'إضافة وحدة')
@section('page-title', 'إضافة وحدة جديدة')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div class="card">
    <div class="card-header"><i class="bi bi-door-open-fill me-2 text-primary"></i>بيانات الوحدة</div>
    <div class="card-body">
        <form method="POST" action="{{ route('units.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">المبنى <span class="text-danger">*</span></label>
                <select name="building_id" class="form-select @error('building_id') is-invalid @enderror" required>
                    <option value="">اختر المبنى</option>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}" {{ old('building_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                @error('building_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">رقم الوحدة <span class="text-danger">*</span></label>
                <input type="text" name="unit_number" class="form-control @error('unit_number') is-invalid @enderror" value="{{ old('unit_number') }}" required>
                @error('unit_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الطابق</label>
                <input type="text" name="floor" class="form-control" value="{{ old('floor') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">النوع</label>
                <select name="type" class="form-select">
                    <option value="residential">سكني</option>
                    <option value="commercial">تجاري</option>
                    <option value="office">مكتب</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    <option value="vacant">شاغرة</option>
                    <option value="rented">مؤجرة</option>
                    <option value="maintenance">صيانة</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الإيجار الأساسي (ريال) <span class="text-danger">*</span></label>
                <input type="number" name="base_rent" class="form-control @error('base_rent') is-invalid @enderror" value="{{ old('base_rent', 0) }}" step="0.01" min="0" required>
                @error('base_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">المساحة (م²)</label>
                <input type="number" name="size" class="form-control" value="{{ old('size') }}" step="0.01" min="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>حفظ</button>
            <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
