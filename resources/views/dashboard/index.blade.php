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
    <div class="col-xl col-md-6">
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
    <div class="col-xl col-md-6">
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
    <div class="col-xl col-md-6">
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
    <div class="col-xl col-md-6">
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
    <div class="col-xl col-md-6">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#ede9fe; color:#6d28d9;"><i class="bi bi-tools"></i></div>
                <div>
                    <div class="text-muted small">مصروفات الصيانة (الشهر)</div>
                    <div class="fw-bold fs-5 text-purple" style="color:#6d28d9;">{{ number_format($maintenanceCostMonth, 0) }} ج.م</div>
                    <div class="small text-muted">{{ $maintenanceOpen }} طلب مفتوح</div>
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
                        <div class="fw-semibold small">{{ $c->tenant?->name ?? '—' }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $c->unit?->building?->name ?? '—' }} — وحدة {{ $c->unit?->unit_number ?? '—' }}</div>
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

{{-- Maintenance Section --}}
<div class="row g-3 mt-1">
    {{-- Summary Card --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-tools me-2 text-purple" style="color:#6d28d9"></i> مصروفات الصيانة</span>
                <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">عرض الكل</a>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="text-muted small">هذا الشهر</div>
                        <div class="fw-bold fs-5" style="color:#6d28d9">{{ number_format($maintenanceCostMonth, 0) }} ج.م</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">هذه السنة</div>
                        <div class="fw-bold fs-5" style="color:#6d28d9">{{ number_format($maintenanceCostYear, 0) }} ج.م</div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="text-center">
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size:.9rem">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $maintenanceOpen }} طلب مفتوح
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{-- Recent Open Requests --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-wrench-adjustable me-2 text-danger"></i>طلبات الصيانة المفتوحة</div>
            <div class="card-body p-0">
                @forelse($maintenanceRecent as $m)
                <div class="d-flex align-items-start px-3 py-2 border-bottom">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $m->unit->building->name }} / وحدة {{ $m->unit->unit_number }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ Str::limit($m->description, 70) }}</div>
                    </div>
                    <div class="text-end ms-3 flex-shrink-0">
                        <span class="badge badge-{{ $m->status }} px-2">{{ ['pending'=>'معلق','in_progress'=>'جاري'][$m->status] }}</span>
                        <div style="font-size:.72rem;color:#aaa">{{ $m->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4"><i class="bi bi-check-circle-fill fs-3 text-success d-block mb-2"></i>لا توجد طلبات مفتوحة</div>
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
