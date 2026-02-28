@extends('layouts.app')
@section('title', 'إنشاء عقد جديد')
@section('page-title', 'إنشاء عقد إيجار جديد')

@push('styles')
<style>
.wizard-step { display:none; }
.wizard-step.active { display:block; }
.step-indicator { display:flex; justify-content:center; gap: 1rem; margin-bottom: 2rem; }
.step-pill { padding: .4rem 1rem; border-radius: 20px; font-size:.82rem; font-weight:700; background:#e4e6ea; color:#666; }
.step-pill.active { background:#1a5276; color:#fff; }
.step-pill.done { background:#d5f5e3; color:#1e8449; }
</style>
@endpush

@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card">
    <div class="card-header">
        <div class="step-indicator">
            <span class="step-pill active" id="pill-1">1. بيانات المستأجر</span>
            <span class="step-pill" id="pill-2">2. الوحدة</span>
            <span class="step-pill" id="pill-3">3. شروط العقد</span>
            <span class="step-pill" id="pill-4">4. الزيادة والغرامات</span>
            <span class="step-pill" id="pill-5">5. رفع العقد</span>
            <span class="step-pill" id="pill-6">6. المراجعة</span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('contracts.store') }}" enctype="multipart/form-data" id="contractForm">
        @csrf

        <!-- Step 1: Tenant -->
        <div class="wizard-step active" id="step-1">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-2"></i>اختيار المستأجر</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">المستأجر <span class="text-danger">*</span></label>
                <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required id="tenantSelect">
                    <option value="">اختر المستأجر...</option>
                    @foreach($tenants as $t)
                    <option value="{{ $t->id }}" {{ (old('tenant_id', request('tenant_id')))==$t->id?'selected':'' }}>{{ $t->name }} — {{ $t->national_id ?? $t->phone }}</option>
                    @endforeach
                </select>
                @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="alert alert-info d-flex gap-2 py-2">
                <i class="bi bi-info-circle-fill"></i>
                <span class="small">لم تجد المستأجر؟ <a href="{{ route('tenants.create') }}">أنشئه أولاً هنا</a> ثم ارجع لهذه الصفحة.</span>
            </div>
        </div>

        <!-- Step 2: Unit -->
        <div class="wizard-step" id="step-2">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-door-open-fill me-2"></i>اختيار الوحدة</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">الوحدة (الشاغرة فقط) <span class="text-danger">*</span></label>
                <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                    <option value="">اختر الوحدة...</option>
                    @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ (old('unit_id', request('unit_id')))==$u->id?'selected':'' }}>
                        {{ $u->building->name }} — وحدة {{ $u->unit_number }} ({{ $u->floor }}) — {{ number_format($u->base_rent) }} ريال
                    </option>
                    @endforeach
                </select>
                @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Step 3: Contract Terms -->
        <div class="wizard-step" id="step-3">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-text-fill me-2"></i>شروط العقد</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">تاريخ البداية <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">تاريخ النهاية <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', now()->addYear()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">الإيجار الأساسي (ريال) <span class="text-danger">*</span></label>
                    <input type="number" name="base_rent" class="form-control" value="{{ old('base_rent', 0) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">دورة الدفع</label>
                    <select name="payment_cycle" class="form-select">
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                        <option value="yearly">سنوي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">يوم الاستحقاق</label>
                    <input type="number" name="due_day" class="form-control" value="{{ old('due_day', 1) }}" min="1" max="31">
                </div>
            </div>
        </div>

        <!-- Step 4: Deposit, Increase, Penalties -->
        <div class="wizard-step" id="step-4">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-gear-fill me-2"></i>التأمين والزيادة والغرامات</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">مبلغ التأمين (ريال)</label>
                    <input type="number" name="security_deposit_amount" class="form-control" value="{{ old('security_deposit_amount', 0) }}" step="0.01" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">سياسة التأمين</label>
                    <select name="deposit_policy" class="form-select">
                        <option value="refundable">قابل للاسترداد</option>
                        <option value="deduct_last_month">يُخصم من آخر شهر</option>
                        <option value="non_refundable">غير قابل للاسترداد</option>
                        <option value="partial">استرداد جزئي</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">نوع الزيادة السنوية</label>
                    <select name="annual_increase_type" class="form-select">
                        <option value="none">لا يوجد</option>
                        <option value="percent">نسبة مئوية</option>
                        <option value="fixed">مبلغ ثابت</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">قيمة الزيادة</label>
                    <input type="number" name="annual_increase_value" class="form-control" value="{{ old('annual_increase_value', 0) }}" step="0.01" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">نوع غرامة التأخير</label>
                    <select name="late_penalty_type" class="form-select">
                        <option value="none">لا يوجد</option>
                        <option value="percent">نسبة مئوية</option>
                        <option value="fixed">مبلغ ثابت</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">قيمة غرامة التأخير</label>
                    <input type="number" name="late_penalty_value" class="form-control" value="{{ old('late_penalty_value', 0) }}" step="0.01" min="0">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">شروط الإنهاء المبكر</label>
                    <textarea name="early_termination_policy" class="form-control" rows="2">{{ old('early_termination_policy') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Step 5: File upload -->
        <div class="wizard-step" id="step-5">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>رفع ملف العقد</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">ملف العقد (PDF أو صورة)</label>
                <input type="file" name="contract_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">الحجم الأقصى 5MB. اختياري.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">ملاحظات إضافية</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Step 6: Review -->
        <div class="wizard-step" id="step-6">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-check2-all me-2"></i>مراجعة وتأكيد</h6>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                راجع البيانات أدناه ثم اضغط <strong>إنشاء العقد</strong>. سيتم تلقائياً توليد جدول الاستحقاقات.
            </div>
            <div id="review-summary" class="bg-light rounded p-3"></div>
        </div>

        <!-- Navigation buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" style="display:none;">
                <i class="bi bi-arrow-right me-1"></i>السابق
            </button>
            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                    التالي <i class="bi bi-arrow-left ms-1"></i>
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                    <i class="bi bi-check-lg me-1"></i>إنشاء العقد
                </button>
            </div>
        </div>
        </form>
    </div>
</div>
</div></div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 6;

function changeStep(dir) {
    const steps = document.querySelectorAll('.wizard-step');
    steps[currentStep - 1].classList.remove('active');
    document.getElementById('pill-' + currentStep).classList.remove('active');
    document.getElementById('pill-' + currentStep).classList.add('done');

    currentStep += dir;
    steps[currentStep - 1].classList.add('active');
    document.getElementById('pill-' + currentStep).classList.add('active');

    document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'block' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';

    if (currentStep === totalSteps) updateReview();
}

function updateReview() {
    const form = document.getElementById('contractForm');
    const get = (n) => form.querySelector(`[name="${n}"]`)?.value ?? '—';
    const getLabel = (n) => { const el = form.querySelector(`[name="${n}"]`); return el?.options?.[el.selectedIndex]?.text ?? el?.value ?? '—'; }

    document.getElementById('review-summary').innerHTML = `
        <div class="row g-2">
            <div class="col-6"><strong>المستأجر:</strong> ${getLabel('tenant_id')}</div>
            <div class="col-6"><strong>الوحدة:</strong> ${getLabel('unit_id')}</div>
            <div class="col-6"><strong>تاريخ البداية:</strong> ${get('start_date')}</div>
            <div class="col-6"><strong>تاريخ النهاية:</strong> ${get('end_date')}</div>
            <div class="col-6"><strong>الإيجار:</strong> ${Number(get('base_rent')).toLocaleString('ar-SA')} ريال</div>
            <div class="col-6"><strong>دورة الدفع:</strong> ${getLabel('payment_cycle')}</div>
            <div class="col-6"><strong>التأمين:</strong> ${Number(get('security_deposit_amount')).toLocaleString('ar-SA')} ريال</div>
            <div class="col-6"><strong>الزيادة السنوية:</strong> ${getLabel('annual_increase_type')} (${get('annual_increase_value')})</div>
            <div class="col-6"><strong>غرامة التأخير:</strong> ${getLabel('late_penalty_type')} (${get('late_penalty_value')})</div>
        </div>`;
}
</script>
@endpush
