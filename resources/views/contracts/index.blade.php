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
                    <td>{{ $c->id }}</td>
                    <td><a href="{{ route('tenants.show', $c->tenant) }}" class="text-decoration-none fw-semibold">{{ $c->tenant->name }}</a></td>
                    <td>{{ $c->unit->building->name }} / <strong>{{ $c->unit->unit_number }}</strong></td>
                    <td>{{ $c->start_date->format('Y-m-d') }}</td>
                    <td class="{{ $c->end_date->isPast() && $c->status=='active' ? 'text-danger fw-semibold' : '' }}">{{ $c->end_date->format('Y-m-d') }}</td>
                    <td>{{ number_format($c->base_rent) }}</td>
                    <td>{{ ['monthly'=>'شهري','quarterly'=>'ربع سنوي','yearly'=>'سنوي'][$c->payment_cycle] }}</td>
                    <td><span class="badge badge-{{ $c->status }} px-2 py-1 rounded-pill">{{ ['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'][$c->status] }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('contracts.show', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('contracts.edit', $c) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
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
