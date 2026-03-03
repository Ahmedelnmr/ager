@extends('layouts.app')
@section('title', 'تعديل مستخدم')
@section('page-title', 'تعديل: ' . $user->name)
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header fw-semibold"><i class="bi bi-pencil-square me-2 text-warning"></i>تعديل بيانات المستخدم</div>
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">الاسم</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-12">
                <hr class="my-1">
                <p class="text-muted small mb-2"><i class="bi bi-shield-lock me-1"></i>تغيير كلمة المرور (اتركها فارغة إذا لا تريد التغيير)</p>
                <label class="form-label fw-semibold">كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control mb-2">
                <label class="form-label fw-semibold">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $user->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">مستخدم نشط</label>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>تحديث</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
