@extends('layouts.app')
@section('title', 'الوحدات')
@section('page-title', 'الوحدات السكنية والتجارية')
@section('content')

<div class="card mb-3 p-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">المبنى</label>
            <select name="building_id" class="form-select form-select-sm">
                <option value="">كل المباني</option>
                @foreach($buildings as $b)
                <option value="{{ $b->id }}" {{ request('building_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">الحالة</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">الكل</option>
                <option value="vacant" {{ request('status')=='vacant'?'selected':'' }}>شاغرة</option>
                <option value="rented" {{ request('status')=='rented'?'selected':'' }}>مؤجرة</option>
                <option value="maintenance" {{ request('status')=='maintenance'?'selected':'' }}>صيانة</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">النوع</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">الكل</option>
                <option value="residential" {{ request('type')=='residential'?'selected':'' }}>سكني</option>
                <option value="commercial" {{ request('type')=='commercial'?'selected':'' }}>تجاري</option>
                <option value="office" {{ request('type')=='office'?'selected':'' }}>مكتبي</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>تصفية</button>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('units.create') }}" class="btn btn-success btn-sm w-100"><i class="bi bi-plus-lg me-1"></i>إضافة وحدة</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead>
                <tr><th>#</th><th>المبنى</th><th>رقم الوحدة</th><th>الطابق</th><th>النوع</th><th>الحالة</th><th>الإيجار</th><th>الحجم م²</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($units as $u)
                <tr>
                    <td data-label="#">{{ $u->id }}</td>
                    <td data-label="المبنى">{{ $u->building?->name ?? '—' }}</td>
                    <td data-label="رقم الوحدة"><a href="{{ route('units.show', $u) }}" class="fw-semibold text-decoration-none">{{ $u->unit_number }}</a></td>
                    <td data-label="الطابق">{{ $u->floor ?? '—' }}</td>
                    <td data-label="النوع">{{ ['residential'=>'سكني','commercial'=>'تجاري','office'=>'مكتبي'][$u->type] ?? $u->type }}</td>
                    <td data-label="الحالة"><span class="badge badge-{{ $u->status }} px-2 py-1 rounded-pill">{{ ['vacant'=>'شاغرة','rented'=>'مؤجرة','maintenance'=>'صيانة'][$u->status] ?? $u->status }}</span></td>
                    <td data-label="الإيجار">{{ number_format($u->base_rent) }}</td>
                    <td data-label="الحجم م²">{{ $u->size ?? '—' }}</td>
                    <td data-label="الإجراءات">
                        <div class="d-flex gap-1">
                            <a href="{{ route('units.show', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('units.edit', $u) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('units.destroy', $u) }}" onsubmit="return confirm('حذف الوحدة؟')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد وحدات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $units->links() }}</div>
@endsection
