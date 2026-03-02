@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
.kpi-card { border-radius: 16px; overflow: hidden; }
.kpi-card .card-body { padding: 1.4rem; }
.kpi-icon { width: 55px; height: 55px; border-radius: 14px; display:flex; align-items:center; justify-content:center; font-size: 1.5rem; }
</style>
@endpush

@section('content')

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#dbeafe; color:#1e40af;"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted small">إيرادات هذا الشهر</div>
                    <div class="fw-bold fs-5">{{ number_format($monthlyIncome, 0) }} ج.م</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#fee2e2; color:#991b1b;"><i class="bi bi-exclamation-octagon-fill"></i></div>
                <div>
                    <div class="text-muted small">إجمالي المتأخرات</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($overdueTotal, 0) }} ج.م</div>
                    <div class="small text-muted">{{ $overdueSchedules }} استحقاق</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#d1fae5; color:#065f46;"><i class="bi bi-door-closed-fill"></i></div>
                <div>
                    <div class="text-muted small">وحدات مؤجرة</div>
                    <div class="fw-bold fs-5">{{ $rentedUnits }} <small class="text-muted fw-normal fs-6">/ {{ $totalUnits }}</small></div>
                    <div class="small text-success">{{ $vacantUnits }} شاغرة</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#fef3c7; color:#92400e;"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="text-muted small">عقود قرب الانتهاء</div>
                    <div class="fw-bold fs-5 text-warning">{{ $endingSoon->count() }}</div>
                    <div class="small text-muted">خلال 30 يوماً</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Chart -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i> إيرادات الأشهر الستة الماضية
            </div>
            <div class="card-body">
                <canvas id="incomeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Ending Contracts -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-calendar-x-fill me-2 text-warning"></i> عقود قرب الانتهاء</span>
                <a href="{{ route('contracts.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-warning">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                @forelse($endingSoon->take(6) as $c)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $c->tenant->name }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $c->unit->building->name }} — وحدة {{ $c->unit->unit_number }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark">{{ now()->diffInDays($c->end_date) }} يوم</span>
                        <div style="font-size:.72rem; color:#aaa;">{{ $c->end_date->format('Y-m-d') }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4"><i class="bi bi-check-circle-fill fs-3 text-success d-block mb-2"></i> لا توجد عقود قرب الانتهاء</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('incomeChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyChart->pluck('month')) !!},
        datasets: [{
            label: 'الإيرادات (ج.م)',
            data: {!! json_encode($monthlyChart->pluck('income')) !!},
            backgroundColor: 'rgba(26,82,118,0.8)',
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true, plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString('ar-SA') } } }
    }
});
</script>
@endpush
