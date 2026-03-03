@extends('layouts.app')
@section('title', 'سجل الأحداث')
@section('page-title', 'سجل الأحداث')
@section('content')
<div class="card">
    <div class="table-responsive">
        @php
            $actionTranslations = [
                'tenant.created' => 'إضافة مستأجر جديد',
                'tenant.updated' => 'تعديل بيانات مستأجر',
                'tenant.deleted' => 'حذف مستأجر',
                'unit.created' => 'إضافة وحدة جديدة',
                'unit.updated' => 'تعديل بيانات وحدة',
                'unit.deleted' => 'حذف وحدة',
                'building.created' => 'إضافة عقار جديد',
                'building.updated' => 'تعديل بيانات عقار',
                'building.deleted' => 'حذف عقار',
                'payment.recorded' => 'تسديد دفعة إيجار',
                'contract.created' => 'إنشاء عقد إيجار',
                'contract.updated' => 'تعديل بيانات عقد',
                'contract.terminated' => 'إنهاء عقد إيجار',
                'maintenance.created' => 'إنشاء طلب صيانة',
                'maintenance.updated' => 'تحديث طلب صيانة',
                'maintenance.deleted' => 'حذف طلب صيانة',
                'user.created' => 'إضافة مستخدم جديد',
                'user.updated' => 'تعديل بيانات مستخدم',
                'user.deleted' => 'حذف مستخدم',
            ];
        @endphp
        <table class="table table-hover table-custom mb-0 align-middle">
            <thead class="table-light"><tr><th>الإجراء</th><th>المستخدم</th><th>التاريخ</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.85rem; min-width: 140px; text-align: right;">
                                <i class="bi bi-activity text-primary me-1"></i>
                                {{ $actionTranslations[$log->action] ?? $log->action }}
                            </span>
                            @if($log->subject)
                                <span class="text-muted small">
                                    @if(str_contains($log->action, 'tenant.'))
                                        (المستأجر: {{ $log->subject->name ?? 'غير معروف' }})
                                    @elseif(str_contains($log->action, 'unit.'))
                                        (الوحدة: {{ $log->subject->unit_number ?? 'غير معروف' }} - {{ $log->subject->building->name ?? '' }})
                                    @elseif(str_contains($log->action, 'building.'))
                                        (المبنى: {{ $log->subject->name ?? 'غير معروف' }})
                                    @elseif(str_contains($log->action, 'contract.'))
                                        (عقد المستأجر: {{ $log->subject->tenant->name ?? 'غير معروف' }} - الوحدة {{ $log->subject->unit->unit_number ?? '' }})
                                    @elseif(str_contains($log->action, 'payment.'))
                                        (تسديد من: {{ $log->subject->contract->tenant->name ?? 'غير معروف' }} - للإيجار رقم #{{ $log->subject->id ?? '' }})
                                    @elseif(str_contains($log->action, 'user.'))
                                        (المستخدم المالك: {{ $log->subject->name ?? 'غير معروف' }})
                                    @else
                                        (#{{ $log->model_id }})
                                    @endif
                                </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">
                            <i class="bi bi-person-circle text-secondary me-1"></i> {{ $log->user?->name ?? 'النظام' }}
                        </div>
                    </td>
                    <td class="text-muted small">
                        <i class="bi bi-clock me-1"></i> {{ $log->created_at?->format('Y-m-d H:i') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">لا توجد سجلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
