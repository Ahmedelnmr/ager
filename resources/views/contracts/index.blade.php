@extends('layouts.app')
@section('title', 'العقود')
@section('page-title', 'العقود')
@section('content')
<div class="card mb-3 p-3">
<form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3">
        <select name="building_id" class="form-select form-select-sm">
            <option value="">كل المباني</option>
            @foreach($buildings as $b)<option value="{{ $b->id }}" {{ request('building_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
            <option value="">كل الحالات</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>نشط</option>
            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>منتهي</option>
            <option value="terminated" {{ request('status')=='terminated'?'selected':'' }}>مُنهى</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i> تصفية</button></div>
    <div class="col-md-5 text-end"><a href="{{ route('contracts.create') }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-plus me-1"></i>إنشاء عقد جديد</a></div>
</form>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>المستأجر</th><th>المبنى / الوحدة</th><th>من</th><th>إلى</th><th>الإيجار</th><th>الدورة</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($contracts as $c)
                <tr>
                    <td data-label="#">{{ $c->id }}</td>
                    <td data-label="المستأجر"><a href="{{ route('tenants.show', $c->tenant) }}" class="text-decoration-none fw-semibold">{{ $c->tenant?->name ?? '—' }}</a></td>
                    <td data-label="المبنى / الوحدة">{{ $c->unit?->building?->name ?? '—' }} / <strong>{{ $c->unit?->unit_number ?? '—' }}</strong></td>
                    <td data-label="من">{{ $c->start_date->format('Y-m-d') }}</td>
                    <td data-label="إلى" class="{{ $c->end_date->isPast() && $c->status=='active' ? 'text-danger fw-semibold' : '' }}">{{ $c->end_date->format('Y-m-d') }}</td>
                    <td data-label="الإيجار">{{ number_format($c->base_rent) }} ج.م</td>
                    <td data-label="الدورة">{{ ['monthly'=>'شهري','quarterly'=>'ربع سنوي','yearly'=>'سنوي'][$c->payment_cycle] }}</td>
                    <td data-label="الحالة">
                        @if($c->status === 'active' && $c->end_date->isPast())
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">منتهي (يحتاج إنهاء)</span>
                        @else
                            <span class="badge badge-{{ $c->status }} px-2 py-1 rounded-pill">{{ ['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'][$c->status] ?? $c->status }}</span>
                        @endif
                    </td>
                    <td data-label="الإجراءات">
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('contracts.show', $c) }}" class="btn btn-sm btn-outline-primary" title="عرض العقد">
                                <i class="bi bi-eye me-1"></i>عرض
                            </a>
                            @if($c->status === 'active')
                            <a href="{{ route('contracts.edit', $c) }}" class="btn btn-sm btn-outline-warning" title="تعديل العقد">
                                <i class="bi bi-pencil me-1"></i>تعديل
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد عقود</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $contracts->links() }}</div>
@endsection
