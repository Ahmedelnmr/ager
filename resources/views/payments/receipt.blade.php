@extends('layouts.app')
@section('title', 'إيصال دفع')
@section('page-title', 'إيصال دفع #' . $payment->id)
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">

<div class="card">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">🏢 نظام الإيجارات الذكي</h4>
            <h5 class="text-muted">إيصال استلام دفعة</h5>
            <hr>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6"><span class="text-muted">رقم الإيصال:</span><br><strong>{{ $payment->rentSchedule?->receipt_number ?? 'RCP-' . str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</strong></div>
            <div class="col-6 text-end"><span class="text-muted">تاريخ الدفع:</span><br><strong>{{ $payment->payment_date->format('Y-m-d') }}</strong></div>
            <div class="col-6"><span class="text-muted">المستأجر:</span><br><strong>{{ $payment->contract->tenant->name }}</strong></div>
            <div class="col-6"><span class="text-muted">الوحدة:</span><br><strong>{{ $payment->contract->unit->building->name }} / {{ $payment->contract->unit->unit_number }}</strong></div>
            <div class="col-6"><span class="text-muted">الفترة:</span><br><strong>{{ $payment->rentSchedule?->period_label ?? '—' }}</strong></div>
            <div class="col-6"><span class="text-muted">طريقة الدفع:</span><br><strong>{{ ['cash'=>'نقد','transfer'=>'تحويل','cheque'=>'شيك'][$payment->payment_method] }}</strong></div>
        </div>
        <div class="bg-primary text-white rounded p-3 text-center mb-3">
            <div class="small text-white-50">المبلغ المستلم</div>
            <div class="fw-bold display-6">{{ number_format($payment->amount) }} ج.م</div>
        </div>
        @if($payment->notes)
        <p class="text-muted small"><strong>ملاحظات:</strong> {{ $payment->notes }}</p>
        @endif
        <div class="text-center text-muted small mt-2">بواسطة: {{ $payment->collectedBy?->name ?? '—' }}</div>
    </div>
    <div class="card-footer d-flex gap-2 justify-content-center">
        <a href="{{ route('payments.download-receipt', $payment) }}" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>تحميل PDF</a>
        <a href="{{ route('contracts.show', $payment->contract) }}" class="btn btn-primary">العودة للعقد</a>
    </div>
</div>
</div></div>
@endsection
