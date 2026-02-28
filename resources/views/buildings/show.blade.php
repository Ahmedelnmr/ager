@extends('layouts.app')
@section('title', $building->name)
@section('page-title', $building->name)
@section('content')

<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('buildings.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    <div class="d-flex gap-2">
        <a href="{{ route('buildings.edit', $building) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('units.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>إضافة وحدة</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div style="font-size:3rem; margin-bottom:.5rem;">🏢</div>
            <h5 class="fw-bold">{{ $building->name }}</h5>
            <p class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $building->address ?? '—' }}, {{ $building->city ?? '' }}</p>
            @if($building->notes)
            <p class="text-muted small">{{ $building->notes }}</p>
            @endif
        </div>
    </div>
    <div class="col-md-8">
        <div class="row g-2 h-100">
            <div class="col-6">
                <div class="card p-3 text-center h-100">
                    <div class="text-muted small">إجمالي الوحدات</div>
                    <div class="fw-bold fs-3 text-primary">{{ $building->units->count() }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3 text-center h-100">
                    <div class="text-muted small">وحدات مؤجرة</div>
                    <div class="fw-bold fs-3 text-success">{{ $building->units->where('status','rented')->count() }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3 text-center h-100">
                    <div class="text-muted small">وحدات شاغرة</div>
                    <div class="fw-bold fs-3 text-warning">{{ $building->units->where('status','vacant')->count() }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3 text-center h-100">
                    <div class="text-muted small">قيد الصيانة</div>
                    <div class="fw-bold fs-3 text-danger">{{ $building->units->where('status','maintenance')->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">وحدات المبنى</div>
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>رقم الوحدة</th><th>الطابق</th><th>النوع</th><th>الحالة</th><th>الإيجار الأساسي</th><th>المستأجر الحالي</th><th></th></tr></thead>
            <tbody>
                @forelse($building->units as $unit)
                <tr>
                    <td>{{ $unit->id }}</td>
                    <td><a href="{{ route('units.show', $unit) }}" class="fw-semibold text-decoration-none">{{ $unit->unit_number }}</a></td>
                    <td>{{ $unit->floor ?? '—' }}</td>
                    <td>{{ ['residential'=>'سكني','commercial'=>'تجاري','office'=>'مكتبي'][$unit->type] ?? $unit->type }}</td>
                    <td><span class="badge badge-{{ $unit->status }} px-2 py-1 rounded-pill">{{ ['vacant'=>'شاغرة','rented'=>'مؤجرة','maintenance'=>'صيانة'][$unit->status] ?? $unit->status }}</span></td>
                    <td>{{ number_format($unit->base_rent) }} ريال</td>
                    <td>{{ $unit->activeContract?->tenant?->name ?? '—' }}</td>
                    <td><a href="{{ route('units.show', $unit) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-3">لا توجد وحدات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
