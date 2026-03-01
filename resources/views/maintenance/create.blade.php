@extends('layouts.app')
@section('title', 'إضافة طلب صيانة')
@section('page-title', 'إضافة طلب صيانة')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-tools me-2 text-warning"></i>بيانات طلب الصيانة</div>
    <div class="card-body">
        <form method="POST" action="{{ route('maintenance.store') }}">
        @csrf
        <div class="row g-3">
            {{-- Building first, then unit --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold">البرج / المبنى <span class="text-danger">*</span></label>
                <select id="buildingFilter" class="form-select" required>
                    <option value="">اختر البرج أولاً...</option>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}" {{ old('building_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الوحدة <span class="text-danger">*</span></label>
                <select name="unit_id" id="unitSelect" class="form-select" required>
                    <option value="">اختر البرج أولاً...</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" data-building="{{ $u->building_id }}" {{ old('unit_id')==$u->id?'selected':'' }}>
                        {{ $u->building->name }} — وحدة {{ $u->unit_number }} (طابق {{ $u->floor }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الحالة</label>
                <select name="status" class="form-select">
                    <option value="pending">معلق — قيد الانتظار</option>
                    <option value="in_progress">جاري التنفيذ</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">اسم المُبلِّغ</label>
                <input type="text" name="reported_by" class="form-control" value="{{ old('reported_by') }}" placeholder="اسم من أبلغ عن المشكلة">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">التكلفة التقديرية (ج.م)</label>
                <div class="input-group">
                    <span class="input-group-text">ج.م</span>
                    <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">وصف المشكلة <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required placeholder="اشرح المشكلة بالتفصيل...">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>حفظ الطلب</button>
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x me-1"></i>إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection

@push('scripts')
<script>
const allUnits = @json($units->map(fn($u) => ['id'=>$u->id,'building_id'=>$u->building_id,'label'=>$u->building->name.' — وحدة '.$u->unit_number.' (طابق '.$u->floor.')']));

document.getElementById('buildingFilter').addEventListener('change', function () {
    const bid = this.value;
    const sel = document.getElementById('unitSelect');
    sel.innerHTML = '<option value="">اختر الوحدة...</option>';
    allUnits.filter(u => !bid || String(u.building_id) === String(bid)).forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = u.label;
        sel.appendChild(opt);
    });
});

// On page load if old value set, trigger building filter
const oldUnit = "{{ old('unit_id') }}";
if (oldUnit) {
    const unit = allUnits.find(u => String(u.id) === String(oldUnit));
    if (unit) {
        document.getElementById('buildingFilter').value = unit.building_id;
        document.getElementById('buildingFilter').dispatchEvent(new Event('change'));
        document.getElementById('unitSelect').value = oldUnit;
    }
}
</script>
@endpush
