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
                            @php $contracts = $t->contracts; @endphp
                            @if($contracts->isEmpty())
                                <button class="btn btn-sm btn-outline-secondary disabled" title="لا توجد عقود لهذا المستأجر">
                                    <i class="bi bi-file-earmark-x me-1"></i>لا عقود
                                </button>
                            @elseif($contracts->count() === 1)
                                {{-- سيناريو 1: عقد واحد → رابط مباشر --}}
                                <a href="{{ route('contracts.show', $contracts->first()->id) }}" class="btn btn-sm btn-outline-info" title="عرض العقد والمدفوعات">
                                    <i class="bi bi-file-earmark-text me-1"></i>العقد والمدفوعات
                                </a>
                            @else
                                {{-- سيناريو 2: أكثر من عقد → Dropdown --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown" title="اختر العقد">
                                        <i class="bi bi-file-earmark-text me-1"></i>العقود ({{ $contracts->count() }})
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @foreach($contracts as $c)
                                        <li>
                                            <a class="dropdown-item small" href="{{ route('contracts.show', $c->id) }}">
                                                <i class="bi bi-building me-1 text-muted"></i>
                                                {{ $c->unit->building->name ?? '—' }} — وحدة {{ $c->unit->unit_number ?? '—' }}
                                                <span class="badge rounded-pill ms-1 {{ $c->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $c->status === 'active' ? 'نشط' : 'منتهي' }}
                                                </span>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
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
