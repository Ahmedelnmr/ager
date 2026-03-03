@extends('layouts.app')
@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم جديد')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header fw-semibold"><i class="bi bi-person-plus me-2 text-primary"></i>بيانات المستخدم</div>
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>إنشاء</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
