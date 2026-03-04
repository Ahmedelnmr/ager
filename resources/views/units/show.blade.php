@extends('layouts.app')
@section('title', 'وحدة ' . $unit->unit_number)
@section('page-title', 'وحدة رقم: ' . $unit->unit_number)
@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('units.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    <div class="d-flex gap-2">
        <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        @if($unit->status == 'vacant')
        <a href="{{ route('contracts.create') }}?unit_id={{ $unit->id }}" class="btn btn-sm btn-success"><i class="bi bi-plus me-1"></i>إنشاء عقد</a>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="fw-bold border-bottom pb-2 mb-3">بيانات الوحدة</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted">المبنى</td><td class="fw-semibold"><a href="{{ route('buildings.show', $unit->building) }}">{{ $unit->building->name }}</a></td></tr>
                <tr><td class="text-muted">رقم الوحدة</td><td class="fw-semibold">{{ $unit->unit_number }}</td></tr>
                <tr><td class="text-muted">الطابق</td><td>{{ $unit->floor ?? '—' }}</td></tr>
                <tr><td class="text-muted">النوع</td><td>{{ ['residential'=>'سكني','commercial'=>'تجاري','office'=>'مكتب'][$unit->type] ?? $unit->type }}</td></tr>
                <tr><td class="text-muted">الحالة</td><td><span class="badge badge-{{ $unit->status }}">{{ ['vacant'=>'شاغرة','rented'=>'مؤجرة','maintenance'=>'صيانة'][$unit->status] }}</span></td></tr>
                <tr><td class="text-muted">الإيجار</td><td class="fw-semibold">{{ number_format($unit->base_rent) }} ج.م</td></tr>
                <tr><td class="text-muted">المساحة</td><td>{{ $unit->size ? $unit->size . ' م²' : '—' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">سجل العقود</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>المستأجر</th><th>من</th><th>إلى</th><th>الحالة</th><th></th></tr></thead>
                    <tbody>
                        @forelse($unit->contracts as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->tenant->name }}</td>
                            <td>{{ $c->start_date->format('Y-m-d') }}</td>
                            <td>{{ $c->end_date->format('Y-m-d') }}</td>
                            <td><span class="badge badge-{{ $c->status }} px-2 rounded-pill">{{ ['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'][$c->status] }}</span></td>
                            <td><a href="{{ route('contracts.show', $c) }}" class="btn btn-xs btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-2">لا توجد عقود</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">طلبات الصيانة</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>الوصف</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                    <tbody>
                        @forelse($unit->maintenanceRequests as $m)
                        <tr>
                            <td>{{ Str::limit($m->description, 50) }}</td>
                            <td><span class="badge badge-{{ $m->status }} px-2">{{ ['pending'=>'معلق','in_progress'=>'جاري','completed'=>'مكتمل','cancelled'=>'ملغي'][$m->status] }}</span></td>
                            <td>{{ $m->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-2">لا توجد طلبات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
