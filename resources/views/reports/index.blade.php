@extends('layouts.app')
@section('title', 'التقارير')
@section('page-title', 'التقارير المالية')
@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-cash-stack me-2"></i>تقرير المدفوعات</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.export-payments') }}">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">من تاريخ</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">إلى تاريخ</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-success w-100"><i class="bi bi-file-earmark-excel me-1"></i>تصدير Excel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-file-earmark-text me-2"></i>تقرير العقود</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.export-contracts') }}">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">الحالة</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">كل الحالات</option>
                                <option value="active">نشطة</option>
                                <option value="expired">منتهية</option>
                                <option value="terminated">مُنهاة</option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-success w-100"><i class="bi bi-file-earmark-excel me-1"></i>تصدير Excel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary KPIs -->
@if(isset($summary))
<div class="row g-3">
    <div class="col-md-3">
        <div class="card p-3 text-center"><div class="text-muted small">إجمالي المدفوعات (الشهر)</div><div class="fw-bold fs-5 text-success">{{ number_format($summary['total_payments']) }} ريال</div></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center"><div class="text-muted small">إجمالي المتأخرات</div><div class="fw-bold fs-5 text-danger">{{ number_format($summary['total_overdue']) }} ريال</div></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center"><div class="text-muted small">عقود نشطة</div><div class="fw-bold fs-5">{{ $summary['active_contracts'] }}</div></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center"><div class="text-muted small">نسبة الإشغال</div><div class="fw-bold fs-5">{{ $summary['occupancy_rate'] }}%</div></div>
    </div>
</div>
@endif
@endsection
