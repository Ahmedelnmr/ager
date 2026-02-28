@extends('layouts.app')
@section('title', 'المستأجرون')
@section('page-title', 'إدارة المستأجرين')
@section('content')
<form method="GET" class="card p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الهوية أو الهاتف..." value="{{ request('search') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>بحث</button></div>
        <div class="col-md-4 text-end"><a href="{{ route('tenants.create') }}" class="btn btn-success w-100"><i class="bi bi-person-plus me-1"></i>إضافة مستأجر</a></div>
    </div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>الاسم</th><th>رقم الهوية</th><th>الهاتف</th><th>البريد</th><th>عدد العقود</th><th>الإجراءات</th></tr></thead>
            <tbody>
                @forelse($tenants as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;background:#1a5276;font-size:.9rem;">{{ substr($t->name,0,1) }}</div>
                            <a href="{{ route('tenants.show', $t) }}" class="text-decoration-none fw-semibold">{{ $t->name }}</a>
                        </div>
                    </td>
                    <td>{{ $t->national_id ?? '—' }}</td>
                    <td>{{ $t->phone ?? '—' }}</td>
                    <td>{{ $t->email ?? '—' }}</td>
                    <td><span class="badge bg-primary rounded-pill">{{ $t->contracts_count }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('tenants.show', $t) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('tenants.edit', $t) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('tenants.destroy', $t) }}" onsubmit="return confirm('حذف المستأجر؟')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد مستأجرون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $tenants->links() }}</div>
@endsection
