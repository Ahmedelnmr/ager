@extends('layouts.app')
@section('title', 'طلبات الصيانة')
@section('page-title', 'طلبات الصيانة')
@section('content')

{{-- Filter Bar --}}
<div class="card mb-3 p-3">
    <form method="GET" class="row g-2 align-items-end">
        {{-- Building --}}
        <div class="col-md-3">
            <label class="form-label small fw-semibold">البرج / المبنى</label>
            <select name="building_id" class="form-select form-select-sm" id="buildingSelect">
                <option value="">كل المباني</option>
                @foreach($buildings as $b)
                <option value="{{ $b->id }}" {{ request('building_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        {{-- Unit (dynamic) --}}
        <div class="col-md-3">
            <label class="form-label small fw-semibold">الوحدة</label>
            <select name="unit_id" class="form-select form-select-sm" id="unitSelect">
                <option value="">كل الوحدات</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->unit_number }}</option>
                @endforeach
            </select>
        </div>
        {{-- Status --}}
        <div class="col-md-2">
            <label class="form-label small fw-semibold">الحالة</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">الكل</option>
                <option value="pending"     {{ request('status')=='pending'      ? 'selected' : '' }}>معلق</option>
                <option value="in_progress" {{ request('status')=='in_progress'  ? 'selected' : '' }}>جاري</option>
                <option value="completed"   {{ request('status')=='completed'    ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled"   {{ request('status')=='cancelled'    ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>تصفية</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary btn-sm w-100">إعادة تعيين</a>
        </div>
    </form>
</div>

{{-- Add button --}}
<div class="d-flex justify-content-end mb-2">
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>إضافة طلب</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>البرج / الوحدة</th><th>الوصف</th><th>البلاغ من</th><th>الحالة</th><th>التكلفة</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td data-label="#">{{ $r->id }}</td>
                    <td data-label="البرج / الوحدة">{{ $r->unit->building->name }} / {{ $r->unit->unit_number }}</td>
                    <td data-label="الوصف">{{ Str::limit($r->description, 60) }}</td>
                    <td data-label="البلاغ من">{{ $r->reported_by ?? '—' }}</td>
                    <td data-label="الحالة"><span class="badge badge-{{ $r->status }} px-2 rounded-pill">{{ ['pending'=>'معلق','in_progress'=>'جاري','completed'=>'مكتمل','cancelled'=>'ملغي'][$r->status] }}</span></td>
                    <td data-label="التكلفة">{{ $r->cost ? number_format($r->cost) . ' ج.م' : '—' }}</td>
                    <td data-label="التاريخ">{{ $r->created_at->format('Y-m-d') }}</td>
                    <td data-label="الإجراءات">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('maintenance.show', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('maintenance.edit', $r) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('maintenance.destroy', $r) }}" class="d-inline" onsubmit="return confirm('حذف؟')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا توجد طلبات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>

@endsection

@push('scripts')
<script>
// When building changes, reload the page with the new building_id to get its units
document.getElementById('buildingSelect').addEventListener('change', function () {
    const url = new URL(window.location.href);
    if (this.value) {
        url.searchParams.set('building_id', this.value);
    } else {
        url.searchParams.delete('building_id');
    }
    url.searchParams.delete('unit_id');
    window.location.href = url.toString();
});
</script>
@endpush
