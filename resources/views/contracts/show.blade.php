@extends('layouts.app')
@section('title', 'عقد رقم ' . $contract->id)
@section('page-title', 'عقد رقم #' . $contract->id)
@section('content')

<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع للعقود</a>
    <div class="d-flex gap-2">
        @if($contract->status === 'active')
        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i>تعديل العقد</a>
        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#terminateModal">
            <i class="bi bi-x-circle me-1"></i>إنهاء العقد
        </button>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 h-100">
            <h6 class="fw-bold border-bottom pb-2">معلومات العقد</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted">المستأجر</td><td><a href="{{ route('tenants.show', $contract->tenant) }}" class="fw-semibold">{{ $contract->tenant->name }}</a></td></tr>
                <tr><td class="text-muted">الوحدة</td><td><a href="{{ route('units.show', $contract->unit) }}">{{ $contract->unit->building->name }} / {{ $contract->unit->unit_number }}</a></td></tr>
                <tr><td class="text-muted">فترة العقد</td><td>{{ $contract->start_date->format('Y-m-d') }} → {{ $contract->end_date->format('Y-m-d') }}</td></tr>
                <tr><td class="text-muted">الإيجار الأساسي</td><td class="fw-semibold">{{ number_format($contract->base_rent) }} ج.م</td></tr>
                <tr><td class="text-muted">دورة الدفع</td><td>{{ ['monthly'=>'شهري','quarterly'=>'ربع سنوي','yearly'=>'سنوي'][$contract->payment_cycle] }}</td></tr>
                <tr><td class="text-muted">يوم الاستحقاق</td><td>{{ $contract->due_day ?? '—' }}</td></tr>
                <tr><td class="text-muted">الحالة</td><td><span class="badge badge-{{ $contract->status }} px-2 py-1 rounded-pill">{{ ['active'=>'نشط','expired'=>'منتهي','terminated'=>'مُنهى'][$contract->status] }}</span></td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 h-100">
            <h6 class="fw-bold border-bottom pb-2">شروط التأمين والزيادة</h6>
            <table class="table table-borderless table-sm mb-0">
                <tr><td class="text-muted">التأمين</td><td>{{ number_format($contract->security_deposit_amount) }} ج.م</td></tr>
                <tr><td class="text-muted">سياسة التأمين</td><td>{{ ['refundable'=>'قابل للاسترداد','deduct_last_month'=>'خصم من آخر شهر','non_refundable'=>'غير مسترد','partial'=>'استرداد جزئي'][$contract->deposit_policy] }}</td></tr>
                <tr><td class="text-muted">الزيادة السنوية</td><td>{{ $contract->annual_increase_type === 'none' ? 'لا يوجد' : ($contract->annual_increase_value . ($contract->annual_increase_type === 'percent' ? '٪' : ' ج.م')) }}</td></tr>
                <tr><td class="text-muted">غرامة التأخير</td><td>{{ $contract->late_penalty_type === 'none' ? 'لا يوجد' : ($contract->late_penalty_value . ($contract->late_penalty_type === 'percent' ? '٪' : ' ج.م')) }}</td></tr>
                @if($contract->early_termination_policy)
                <tr><td class="text-muted">الإنهاء المبكر</td><td><small>{{ $contract->early_termination_policy }}</small></td></tr>
                @endif
                @if($contract->file_path)
                <tr><td class="text-muted">ملف العقد</td><td><a href="{{ asset('storage/'.$contract->file_path) }}" target="_blank"><i class="bi bi-file-earmark-text me-1"></i>عرض / تحميل</a></td></tr>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Rent Schedules -->
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check me-2"></i>جدول الاستحقاقات</span>
        <span class="badge bg-secondary">{{ $contract->rentSchedules->count() }} استحقاق</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover table-custom mb-0">
            <thead class="table-light"><tr><th>الفترة</th><th>تاريخ الاستحقاق</th><th>المبلغ الأساسي</th><th>الغرامة</th><th>الخصم</th><th>الإجمالي</th><th>المدفوع</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
                @foreach($contract->rentSchedules as $s)
                <tr>
                    <td data-label="الفترة">{{ $s->period_label }}</td>
                    <td data-label="الاستحقاق">{{ $s->due_date->format('Y-m-d') }}</td>
                    <td data-label="المبلغ">{{ number_format($s->base_amount) }}</td>
                    <td data-label="الغرامة" class="{{ $s->penalty_amount > 0 ? 'text-danger' : '' }}">{{ number_format($s->penalty_amount) }}</td>
                    <td data-label="الخصم">{{ number_format($s->discount_amount) }}</td>
                    <td data-label="الإجمالي" class="fw-semibold">{{ number_format($s->final_amount) }}</td>
                    <td data-label="المدفوع" class="{{ $s->paid_amount < $s->final_amount ? 'text-warning' : 'text-success' }}">{{ number_format($s->paid_amount) }}</td>
                    <td data-label="الحالة"><span class="badge badge-{{ $s->status }} px-2 rounded-pill">{{ ['due'=>'مستحق','paid'=>'مدفوع','partial'=>'جزئي','overdue'=>'متأخر'][$s->status] }}</span></td>
                    <td data-label="الإجراءات">
                        @if($s->status !== 'paid')
                        <a href="{{ route('payments.create', $s) }}" class="btn btn-xs btn-sm btn-success py-0 px-2" title="تسجيل دفعة">
                            <i class="bi bi-cash me-1"></i>دفع
                        </a>
                        @endif
                        <a href="{{ route('rent-schedules.show', $s) }}" class="btn btn-xs btn-sm btn-outline-primary py-0 px-2" title="عرض التفاصيل"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Payments -->
<div class="card">
    <div class="card-header"><i class="bi bi-cash-coin me-2"></i>المدفوعات المسجلة</div>
    <div class="table-responsive">
        <table class="table table-sm table-custom mb-0">
            <thead class="table-light"><tr><th>رقم الإيصال</th><th>الفترة</th><th>المبلغ</th><th>طريقة الدفع</th><th>تاريخ الدفع</th><th>بواسطة</th><th></th></tr></thead>
            <tbody>
                @forelse($contract->payments as $p)
                <tr>
                    <td data-label="الإيصال">{{ $p->rentSchedule?->receipt_number ?? '#' . $p->id }}</td>
                    <td data-label="الفترة">{{ $p->rentSchedule?->period_label ?? '—' }}</td>
                    <td data-label="المبلغ" class="fw-semibold text-success">{{ number_format($p->amount) }}</td>
                    <td data-label="طريقة الدفع">{{ ['cash'=>'نقد','transfer'=>'تحويل','cheque'=>'شيك'][$p->payment_method] }}</td>
                    <td data-label="التاريخ">{{ $p->payment_date->format('Y-m-d') }}</td>
                    <td data-label="بواسطة">{{ $p->collectedBy?->name ?? '—' }}</td>
                    <td data-label="PDF"><a href="{{ route('payments.download-receipt', $p) }}" class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-file-pdf"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">لا توجد مدفوعات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

{{-- Terminate Contract Modal --}}
<div class="modal fade" id="terminateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>إنهاء العقد</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('contracts.terminate', $contract) }}">
        @csrf @method('PATCH')
        <div class="modal-body">
          <div class="alert alert-warning py-2 small">
            <i class="bi bi-info-circle me-1"></i>
            مبلغ التأمين المسجل: <strong>{{ number_format($contract->security_deposit_amount) }} ج.م</strong>
            — السياسة الافتراضية: <strong>{{ ['refundable'=>'قابل للاسترداد','deduct_last_month'=>'خصم من آخر شهر','non_refundable'=>'غير مسترد','partial'=>'جزئي'][$contract->deposit_policy] }}</strong>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">كيف تريد معالجة التأمين الآن؟</label>
            <select name="deposit_action" class="form-select" id="depositActionSel">
              <option value="keep">احتفظ بالتأمين (لا استرداد)</option>
              <option value="refund">ارجع التأمين كاملاً للمستأجر</option>
              <option value="partial">استرداد جزئي (حدد المبلغ)</option>
            </select>
          </div>
          <div class="mb-3" id="refundAmountBox" style="display:none;">
            <label class="form-label fw-semibold">المبلغ المُسترد (ج.م)</label>
            <div class="input-group">
              <span class="input-group-text">ج.م</span>
              <input type="number" name="deposit_refund_amount" class="form-control" step="0.01" min="0" max="{{ $contract->security_deposit_amount }}" value="0" placeholder="مثال: 500">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x me-1"></i>إلغاء</button>
          <button type="submit" class="btn btn-danger" onclick="return confirm('تأكيد إنهاء العقد؟')"><i class="bi bi-check-lg me-1"></i>تأكيد الإنهاء</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('depositActionSel')?.addEventListener('change', function () {
    document.getElementById('refundAmountBox').style.display = this.value === 'partial' ? 'block' : 'none';
});
</script>
@endpush
