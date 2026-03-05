@extends('layouts.app')
@section('title', 'المباني')
@section('page-title', 'الأبراج والمباني')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div><i class="bi bi-building-fill text-primary me-2"></i><strong>إجمالي:</strong> {{ $buildings->total() }} مبنى</div>
    <a href="{{ route('buildings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> إضافة مبنى
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th><th>اسم المبنى</th><th>المدينة</th>
                    <th>إجمالي الوحدات</th><th>مؤجرة</th><th>شاغرة</th><th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buildings as $b)
                <tr>
                    <td data-label="#">{{ $b->id }}</td>
                    <td data-label="اسم المبنى"><a href="{{ route('buildings.show', $b) }}" class="fw-semibold text-decoration-none">{{ $b->name }}</a></td>
                    <td data-label="المدينة">{{ $b->city ?? '—' }}</td>
                    <td data-label="إجمالي الوحدات"><span class="badge bg-secondary">{{ $b->units_count }}</span></td>
                    <td data-label="مؤجرة"><span class="badge badge-rented px-2 py-1 rounded-pill">{{ $b->active_units_count }}</span></td>
                    <td data-label="شاغرة"><span class="badge badge-vacant px-2 py-1 rounded-pill">{{ $b->vacant_units_count }}</span></td>
                    <td data-label="الإجراءات">
                        <div class="d-flex gap-1">
                            <a href="{{ route('buildings.show', $b) }}" class="btn btn-sm btn-outline-primary" title="عرض"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('buildings.edit', $b) }}" class="btn btn-sm btn-outline-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('buildings.destroy', $b) }}" onsubmit="return confirm('هل تريد حذف هذا المبنى؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا توجد مبانٍ مسجلة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $buildings->links() }}</div>
@endsection
