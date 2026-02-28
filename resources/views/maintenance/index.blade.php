@extends('layouts.app')
@section('title', 'طلبات الصيانة')
@section('page-title', 'طلبات الصيانة')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>إضافة طلب</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>الوحدة</th><th>الوصف</th><th>البلاغ من</th><th>الحالة</th><th>التكلفة</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->unit->building->name }} / {{ $r->unit->unit_number }}</td>
                    <td>{{ Str::limit($r->description, 60) }}</td>
                    <td>{{ $r->reported_by ?? '—' }}</td>
                    <td><span class="badge badge-{{ $r->status }} px-2 rounded-pill">{{ ['pending'=>'معلق','in_progress'=>'جاري','completed'=>'مكتمل','cancelled'=>'ملغي'][$r->status] }}</span></td>
                    <td>{{ $r->cost ? number_format($r->cost) . ' ريال' : '—' }}</td>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('maintenance.edit', $r) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('maintenance.destroy', $r) }}" class="d-inline" onsubmit="return confirm('حذف؟')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
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
