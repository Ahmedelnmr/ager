@extends('layouts.app')
@section('title', 'إنشاء عقد جديد')
@section('page-title', 'إنشاء عقد إيجار جديد')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" rel="stylesheet"/>
<style>
.wizard-step { display:none; }
.wizard-step.active { display:block; }
.step-indicator { display:flex; justify-content:center; gap:.7rem; margin-bottom:2rem; flex-wrap:wrap; }
.step-pill { padding:.4rem 1rem; border-radius:20px; font-size:.8rem; font-weight:700; background:#e4e6ea; color:#666; }
.step-pill.active { background:#1a5276; color:#fff; }
.step-pill.done { background:#d5f5e3; color:#1e8449; }
.deposit-extra { display:none; }
</style>
@endpush

@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card">
    <div class="card-header">
        <div class="step-indicator">
            <span class="step-pill active" id="pill-1">1. المستأجر</span>
            <span class="step-pill" id="pill-2">2. الوحدة</span>
            <span class="step-pill" id="pill-3">3. شروط العقد</span>
            <span class="step-pill" id="pill-4">4. التأمين والغرامات</span>
            <span class="step-pill" id="pill-5">5. رفع العقد</span>
            <span class="step-pill" id="pill-6">6. المراجعة</span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('contracts.store') }}" enctype="multipart/form-data" id="contractForm">
        @csrf

        {{-- Step 1: Tenant (Select2 searchable) --}}
        <div class="wizard-step active" id="step-1">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-2"></i>اختيار المستأجر</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">المستأجر <span class="text-danger">*</span></label>
                <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required id="tenantSelect">
                    <option value="">ابحث أو اختر المستأجر...</option>
                    @foreach($tenants as $t)
                    <option value="{{ $t->id }}" {{ old('tenant_id', request('tenant_id'))==$t->id?'selected':'' }}>
                        {{ $t->name }} — {{ $t->national_id ?? $t->phone }}
                    </option>
                    @endforeach
                </select>
                @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="alert alert-info d-flex gap-2 py-2">
                <i class="bi bi-info-circle-fill"></i>
                <span class="small">لم تجد المستأجر؟ <a href="{{ route('tenants.create') }}" target="_blank">أنشئه هنا</a> ثم ارجع وأعد تحميل الصفحة.</span>
            </div>
        </div>

        {{-- Step 2: Unit grouped by Building --}}
        <div class="wizard-step" id="step-2">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-door-open-fill me-2"></i>اختيار الوحدة</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">البرج أولاً</label>
                <select id="buildingFilter" class="form-select mb-2">
                    <option value="">— كل الأبراج —</option>
                    @foreach($buildings as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">الوحدة (الشاغرة فقط) <span class="text-danger">*</span></label>
                <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required id="unitSelect">
                    <option value="">اختر البرج أولاً...</option>
                    @foreach($buildings as $b)
                        @foreach($b->units->where('status','vacant') as $u)
                        <option value="{{ $u->id }}"
                            data-building="{{ $b->id }}"
                            data-rent="{{ $u->base_rent }}"
                            {{ old('unit_id', request('unit_id'))==$u->id?'selected':'' }}>
                            {{ $b->name }} — وحدة {{ $u->unit_number }} (طابق {{ $u->floor }}) — {{ number_format($u->base_rent) }} ج.م
                        </option>
                        @endforeach
                    @endforeach
                </select>
                @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text" id="unitRentHint"></div>
            </div>
        </div>

        {{-- Step 3: Contract Terms --}}
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
                    <label class="form-label fw-semibold">الإيجار الأساسي (ج.م) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">ج.م</span>
                        <input type="number" name="base_rent" id="baseRentInput" class="form-control" value="{{ old('base_rent', 0) }}" step="0.01" min="0" required>
                    </div>
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
                    <label class="form-label fw-semibold">يوم الاستحقاق <small class="text-muted">(1-31)</small></label>
                    <input type="number" name="due_day" class="form-control" value="{{ old('due_day', 1) }}" min="1" max="31">
                </div>
            </div>
        </div>

        {{-- Step 4: Deposit, Increase, Penalties --}}
        <div class="wizard-step" id="step-4">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-lock-fill me-2"></i>التأمين والزيادة السنوية والغرامات</h6>
            <div class="row g-3">
                {{-- Deposit --}}
                <div class="col-12"><h6 class="text-secondary border-bottom pb-1 mb-0">مبلغ التأمين</h6></div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">مبلغ التأمين (ج.م)</label>
                    <div class="input-group">
                        <span class="input-group-text">ج.م</span>
                        <input type="number" name="security_deposit_amount" class="form-control" value="{{ old('security_deposit_amount', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="form-text">يُضاف تلقائياً للإيرادات عند إنشاء العقد.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">سياسة التأمين عند انتهاء العقد</label>
                    <select name="deposit_policy" class="form-select" id="depositPolicySelect">
                        <option value="refundable">قابل للاسترداد بالكامل</option>
                        <option value="deduct_last_month">يُخصم من آخر شهر</option>
                        <option value="non_refundable">غير قابل للاسترداد</option>
                        <option value="partial">استرداد جزئي</option>
                    </select>
                </div>
                {{-- Partial refund options --}}
                <div class="col-12 deposit-extra" id="partialRefundBox">
                    <div class="card bg-light border-0 p-3">
                        <label class="form-label fw-semibold">تفاصيل الاسترداد الجزئي</label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">نوع الاسترداد</label>
                                <select name="partial_refund_type" class="form-select form-select-sm">
                                    <option value="percent">نسبة مئوية من التأمين</option>
                                    <option value="fixed">مبلغ ثابت (ج.م)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">القيمة</label>
                                <input type="number" name="partial_refund_value" class="form-control form-control-sm" value="{{ old('partial_refund_value', 50) }}" step="0.01" min="0" placeholder="مثال: 50 أو 500">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="alert alert-warning py-2 px-3 small mb-0 w-100">
                                    <i class="bi bi-info-circle me-1"></i>
                                    عند الإنهاء يمكن تعديل هذا المبلغ حسب الموقف.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Annual increase --}}
                <div class="col-12"><h6 class="text-secondary border-bottom pb-1 mb-0 mt-2">الزيادة السنوية</h6></div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">نوع الزيادة</label>
                    <select name="annual_increase_type" class="form-select">
                        <option value="none">لا يوجد</option>
                        <option value="percent">نسبة مئوية %</option>
                        <option value="fixed">مبلغ ثابت (ج.م)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">قيمة الزيادة</label>
                    <input type="number" name="annual_increase_value" class="form-control" value="{{ old('annual_increase_value', 0) }}" step="0.01" min="0">
                </div>

                {{-- Late penalty --}}
                <div class="col-12"><h6 class="text-secondary border-bottom pb-1 mb-0 mt-2">غرامة التأخير</h6></div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">نوع الغرامة</label>
                    <select name="late_penalty_type" class="form-select">
                        <option value="none">لا يوجد</option>
                        <option value="percent">نسبة % من الإيجار</option>
                        <option value="fixed">مبلغ ثابت (ج.م)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">قيمة الغرامة</label>
                    <input type="number" name="late_penalty_value" class="form-control" value="{{ old('late_penalty_value', 0) }}" step="0.01" min="0">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">شروط الإنهاء المبكر</label>
                    <textarea name="early_termination_policy" class="form-control" rows="2" placeholder="مثال: غرامة شهر إيجار عند الإنهاء قبل 3 أشهر من انتهاء العقد">{{ old('early_termination_policy') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Step 5: File --}}
        <div class="wizard-step" id="step-5">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>رفع ملف العقد</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">ملف العقد (PDF أو صورة)</label>
                <input type="file" name="contract_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-text">الحجم الأقصى 5MB. اختياري.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">ملاحظات إضافية</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Step 6: Review --}}
        <div class="wizard-step" id="step-6">
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-check2-all me-2"></i>مراجعة وتأكيد</h6>
            <div class="alert alert-success">
                <i class="bi bi-info-circle me-2"></i>
                راجع البيانات أدناه ثم اضغط <strong>إنشاء العقد</strong>. سيتم تلقائياً توليد جدول الاستحقاقات وإضافة التأمين للإيرادات.
            </div>
            <div id="review-summary" class="bg-light rounded p-3"></div>
        </div>

        {{-- Navigation --}}
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    // Select2 for tenant
    $('#tenantSelect').select2({
        theme: 'bootstrap-5',
        placeholder: 'ابحث باسم المستأجر أو رقم الهوية...',
        language: 'ar',
        width: '100%',
        dir: 'rtl',
    });

    // Building → Unit cascading filter
    $('#buildingFilter').on('change', function () {
        const bid = $(this).val();
        $('#unitSelect option').each(function () {
            const ob = $(this).data('building');
            if (!bid || String(ob) === String(bid) || !ob) {
                $(this).show().prop('disabled', false);
            } else {
                $(this).hide().prop('disabled', true);
            }
        });
        $('#unitSelect').val('').trigger('change');
        $('#unitRentHint').text('');
    });

    // Hint on unit select
    $('#unitSelect').on('change', function () {
        const rent = $(this).find(':selected').data('rent');
        if (rent) {
            $('#unitRentHint').text('الإيجار الأساسي للوحدة: ' + Number(rent).toLocaleString('ar-EG') + ' ج.م');
            $('#baseRentInput').val(rent);
        }
    });

    // Partial refund toggle
    $('#depositPolicySelect').on('change', function () {
        if ($(this).val() === 'partial') {
            $('#partialRefundBox').show();
        } else {
            $('#partialRefundBox').hide();
        }
    });
});

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
    const getLabel = (n) => {
        const el = form.querySelector(`[name="${n}"]`);
        return el?.options?.[el.selectedIndex]?.text ?? el?.value ?? '—';
    };

    const depositAmt = Number(get('security_deposit_amount') || 0).toLocaleString('ar-EG');
    const baseRent   = Number(get('base_rent') || 0).toLocaleString('ar-EG');

    document.getElementById('review-summary').innerHTML = `
        <div class="row g-2 small">
            <div class="col-6"><strong>المستأجر:</strong> ${getLabel('tenant_id')}</div>
            <div class="col-6"><strong>الوحدة:</strong> ${getLabel('unit_id')}</div>
            <div class="col-6"><strong>تاريخ البداية:</strong> ${get('start_date')}</div>
            <div class="col-6"><strong>تاريخ النهاية:</strong> ${get('end_date')}</div>
            <div class="col-6"><strong>الإيجار:</strong> ${baseRent} ج.م</div>
            <div class="col-6"><strong>دورة الدفع:</strong> ${getLabel('payment_cycle')}</div>
            <div class="col-6"><strong>التأمين:</strong> ${depositAmt} ج.م</div>
            <div class="col-6"><strong>سياسة التأمين:</strong> ${getLabel('deposit_policy')}</div>
            <div class="col-6"><strong>الزيادة السنوية:</strong> ${getLabel('annual_increase_type')} (${get('annual_increase_value')})</div>
            <div class="col-6"><strong>غرامة التأخير:</strong> ${getLabel('late_penalty_type')} (${get('late_penalty_value')})</div>
        </div>`;
}
</script>
@endpush
