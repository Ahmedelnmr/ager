@extends('layouts.app')
@section('title', 'المستخدمون')
@section('page-title', 'إدارة المستخدمين')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>إضافة مستخدم</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>الدور</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?? '—' }}</td>
                    <td>
                        @foreach($u->roles as $r)
                        <span class="badge bg-primary rounded-pill">{{ $r->name }}</span>
                        @endforeach
                    </td>
                    <td><span class="badge {{ $u->is_active ? 'badge-active' : 'badge-expired' }} px-2 rounded-pill">{{ $u->is_active ? 'نشط' : 'موقوف' }}</span></td>
                    <td><a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد مستخدمون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
