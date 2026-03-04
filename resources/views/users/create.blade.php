@extends('layouts.app')
@section('title', 'إضافة مستخدم')
@section('page-title', 'إضافة مستخدم جديد')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header fw-semibold"><i class="bi bi-person-plus me-2 text-primary"></i>بيانات المستخدم</div>
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}" id="createUserForm" novalidate>
        @csrf
        <div class="row g-3">

            {{-- Name --}}
            <div class="col-12">
                <label class="form-label fw-semibold">الاسم <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required minlength="2">
                <div class="invalid-feedback">يرجى إدخال الاسم (حرفين على الأقل).</div>
                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="col-12">
                <label class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح.</div>
                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            {{-- Password --}}
            <div class="col-12">
                <label class="form-label fw-semibold">كلمة المرور <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required minlength="8">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                    <div class="invalid-feedback">كلمة المرور يجب أن تكون 8 أحرف على الأقل.</div>
                </div>
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Password Confirmation --}}
            <div class="col-12">
                <label class="form-label fw-semibold">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirm" tabindex="-1">
                        <i class="bi bi-eye" id="eyeIconConfirm"></i>
                    </button>
                    <div class="invalid-feedback">كلمة المرور غير متطابقة.</div>
                </div>
            </div>

            {{-- Phone --}}
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

@push('scripts')
<script>
// Toggle show/hide password
function togglePass(btnId, inputId, iconId) {
    document.getElementById(btnId).addEventListener('click', function () {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
}
togglePass('togglePassword', 'password', 'eyeIcon');
togglePass('toggleConfirm',  'password_confirmation', 'eyeIconConfirm');

// Client-side validation
document.getElementById('createUserForm').addEventListener('submit', function (e) {
    const form   = this;
    const pw     = document.getElementById('password');
    const pwConf = document.getElementById('password_confirmation');

    // Reset previous custom validity
    pwConf.setCustomValidity('');

    if (pw.value !== pwConf.value) {
        pwConf.setCustomValidity('كلمة المرور غير متطابقة');
    }

    if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    form.classList.add('was-validated');
});
</script>
@endpush
@endsection
