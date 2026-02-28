@extends('layouts.app')
@section('title', 'تعديل وحدة')
@section('page-title', 'تعديل الوحدة: ' . $unit->unit_number)
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil-fill me-2 text-warning"></i>تعديل بيانات الوحدة</div>
    <div class="card-body">
        <form method="POST" action="{{ route('units.update', $unit) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">المبنى</label>
                <select name="building_id" class="form-select" required>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}" {{ $unit->building_id==$b->id?'selected':'' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">رقم الوحدة</label>
                <input type="text" name="unit_number" class="form-control" value="{{ old('unit_number', $unit->unit_number) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الطابق</label>
                <input type="text" name="floor" class="form-control" value="{{ old('floor', $unit->floor) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">النوع</label>
                <select name="type" class="form-select">
                    @foreach(['residential'=>'سكني','commercial'=>'تجاري','office'=>'مكتب'] as $v=>$l)
                    <option value="{{ $v }}" {{ $unit->type==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    @foreach(['vacant'=>'شاغرة','rented'=>'مؤجرة','maintenance'=>'صيانة'] as $v=>$l)
                    <option value="{{ $v }}" {{ $unit->status==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الإيجار الأساسي (ريال)</label>
                <input type="number" name="base_rent" class="form-control" value="{{ old('base_rent', $unit->base_rent) }}" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">المساحة (م²)</label>
                <input type="number" name="size" class="form-control" value="{{ old('size', $unit->size) }}" step="0.01" min="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $unit->notes) }}</textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>تحديث</button>
            <a href="{{ route('units.show', $unit) }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
