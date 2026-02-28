@extends('layouts.app')
@section('title', 'تعديل طلب صيانة')
@section('page-title', 'تعديل طلب صيانة')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil me-2"></i>تعديل الطلب #{{ $maintenance->id }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('maintenance.update', $maintenance) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">الوحدة</label>
                <select name="unit_id" class="form-select" required>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ $maintenance->unit_id==$u->id?'selected':'' }}>{{ $u->building->name }} — {{ $u->unit_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    @foreach(['pending'=>'معلق','in_progress'=>'جاري','completed'=>'مكتمل','cancelled'=>'ملغي'] as $v=>$l)
                    <option value="{{ $v }}" {{ $maintenance->status==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">اسم البالغ</label>
                <input type="text" name="reported_by" class="form-control" value="{{ old('reported_by', $maintenance->reported_by) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">التكلفة (ريال)</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost', $maintenance->cost) }}" step="0.01" min="0">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">وصف المشكلة</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $maintenance->description) }}</textarea>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>تحديث</button>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
