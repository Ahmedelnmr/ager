@extends('layouts.app')
@section('title', 'المستأجرون')
@section('page-title', 'إدارة المستأجرين')
@section('content')
<form method="GET" class="card p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الهوية أو الهاتف..." value="{{ request('search') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>بحث</button></div>
        <div class="col-md-4 text-end"><a href="{{ route('tenants.create') }}" class="btn btn-success w-100"><i class="bi bi-person-plus me-1"></i>إضافة مستأجر جديد</a></div>
    </div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>الاسم</th><th>رقم الهوية</th><th>الهاتف</th><th>البريد</th><th>العقود</th><th>الإجراءات</th></tr></thead>
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
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('tenants.show', $t) }}" class="btn btn-sm btn-outline-primary" title="عرض الملف الشخصي">
                                <i class="bi bi-eye me-1"></i>عرض
                            </a>
                            <a href="{{ route('tenants.edit', $t) }}" class="btn btn-sm btn-outline-warning" title="تعديل بيانات المستأجر">
                                <i class="bi bi-pencil me-1"></i>تعديل
                            </a>
                            <a href="{{ route('contracts.index', ['tenant_id' => $t->id]) }}" class="btn btn-sm btn-outline-info" title="عقود ومدفوعات واستحقاقات المستأجر">
                                <i class="bi bi-file-earmark-text me-1"></i>العقود والمدفوعات
                            </a>
                            <form method="POST" action="{{ route('tenants.destroy', $t) }}" onsubmit="return confirm('حذف المستأجر {{ $t->name }}؟')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="حذف المستأجر">
                                    <i class="bi bi-trash me-1"></i>حذف
                                </button>
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
