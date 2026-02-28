@extends('layouts.app')
@section('title', $tenant->name)
@section('page-title', $tenant->name)
@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('tenants.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    <div class="d-flex gap-2">
        <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('contracts.create') }}?tenant_id={{ $tenant->id }}" class="btn btn-sm btn-primary"><i class="bi bi-file-earmark-plus me-1"></i>إنشاء عقد</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto" style="width:80px;height:80px;background:#1a5276;font-size:2rem;margin-bottom:1rem;">{{ substr($tenant->name,0,1) }}</div>
            <h5 class="fw-bold">{{ $tenant->name }}</h5>
            <p class="text-muted small mb-1">{{ $tenant->national_id ?? '' }}</p>
            <p class="mb-1"><i class="bi bi-telephone me-1"></i>{{ $tenant->phone ?? '—' }}</p>
            <p class="mb-1"><i class="bi bi-envelope me-1"></i>{{ $tenant->email ?? '—' }}</p>
            @if($tenant->address)<p class="text-muted small">{{ $tenant->address }}</p>@endif
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-bold">العقود</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>#</th><th>المبنى / الوحدة</th><th>من</th><th>إلى</th><th>الإيجار</th><th>الحالة</th><th></th></tr></thead>
                    <tbody>
                        @forelse($tenant->contracts as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->unit->building->name }} / {{ $c->unit->unit_number }}</td>
                            <td>{{ $c->start_date->format('Y-m-d') }}</td>
                            <td>{{ $c->end_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($c->base_rent) }}</td>
                            <td><span class="badge badge-{{ $c->status }}">{{ ['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'][$c->status] }}</span></td>
                            <td><a href="{{ route('contracts.show', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">لا توجد عقود</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
