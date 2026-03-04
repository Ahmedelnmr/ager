<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال دفع</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; font-size: 13px; color: #222; }
        .header { text-align:center; border-bottom: 2px solid #1a5276; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { color: #1a5276; font-size: 20px; margin: 0; }
        .header h3 { color: #666; font-size: 14px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table td, table th { padding: 7px 10px; }
        table th { background: #f0f4f8; font-weight: bold; width: 40%; }
        .amount-box { background: #1a5276; color: white; text-align: center; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .amount-box .label { font-size: 12px; opacity: .8; }
        .amount-box .amount { font-size: 28px; font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #888; border-top: 1px solid #ddd; padding-top: 12px; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 11px; background: #d5f5e3; color: #1e8449; }
    </style>
</head>
<body>
<div class="header">
    <h1>🏢 نظام الإيجارات الذكي</h1>
    <h3>Smart Flexible Rental Management System</h3>
</div>

<strong>إيصال استلام دفعة إيجار</strong>

<table>
    <tr><th>رقم الإيصال</th><td>{{ $payment->rentSchedule?->receipt_number ?? 'RCP-' . str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</td></tr>
    <tr><th>تاريخ الإصدار</th><td>{{ now()->format('Y-m-d H:i') }}</td></tr>
    <tr><th>اسم المستأجر</th><td>{{ $payment->contract->tenant->name }}</td></tr>
    <tr><th>رقم الهوية</th><td>{{ $payment->contract->tenant->national_id ?? '—' }}</td></tr>
    <tr><th>اسم المبنى</th><td>{{ $payment->contract->unit->building->name }}</td></tr>
    <tr><th>رقم الوحدة</th><td>{{ $payment->contract->unit->unit_number }}</td></tr>
    <tr><th>الفترة</th><td>{{ $payment->rentSchedule?->period_label ?? '—' }}</td></tr>
    <tr><th>تاريخ الدفع</th><td>{{ $payment->payment_date->format('Y-m-d') }}</td></tr>
    <tr><th>طريقة الدفع</th><td>{{ ['cash'=>'نقداً','transfer'=>'تحويل بنكي','cheque'=>'شيك'][$payment->payment_method] }}</td></tr>
    @if($payment->transaction_ref)
    <tr><th>رقم المرجع</th><td>{{ $payment->transaction_ref }}</td></tr>
    @endif
    <tr><th>مستلم بواسطة</th><td>{{ $payment->collectedBy?->name ?? '—' }}</td></tr>
</table>

<div class="amount-box">
    <div class="label">المبلغ المستلم</div>
    <div class="amount">{{ number_format($payment->amount, 2) }} ج.م</div>
</div>

@if($payment->notes)
<p><strong>ملاحظات:</strong> {{ $payment->notes }}</p>
@endif

<div class="footer">
    تم إصدار هذا الإيصال آلياً بواسطة نظام الإيجارات الذكي — {{ now()->format('Y-m-d H:i') }}
</div>
</body>
</html>
