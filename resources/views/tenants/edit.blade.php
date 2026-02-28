@extends('layouts.app')
@section('title', 'تعديل مستأجر')
@section('page-title', 'تعديل: ' . $tenant->name)
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil-fill me-2 text-warning"></i>تعديل بيانات المستأجر</div>
    <div class="card-body">
        <form method="POST" action="{{ route('tenants.update', $tenant) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">الاسم الكامل</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">رقم الهوية</label>
                <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $tenant->national_id) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">الهاتف</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">العنوان</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $tenant->address) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">صورة شخصية جديدة (اترك فارغاً للإبقاء على القديمة)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>تحديث</button>
            <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection
