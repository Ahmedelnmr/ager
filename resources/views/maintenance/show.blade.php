@extends('layouts.app')
@section('title', 'طلب صيانة #' . $maintenance->id)
@section('page-title', 'تفاصيل طلب الصيانة #' . $maintenance->id)
@section('content')

<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i>رجوع للصيانة
    </a>
    <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-sm btn-warning">
        <i class="bi bi-pencil me-1"></i>تعديل الطلب
    </a>
</div>

<div class="row g-3">
    {{-- Main info card --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-tools me-2 text-warning"></i>بيانات الطلب</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:35%">البرج / الوحدة</td>
                        <td class="fw-semibold">
                            {{ $maintenance->unit->building->name ?? '—' }}
                            / وحدة {{ $maintenance->unit->unit_number ?? '—' }}
                            @if($maintenance->unit->floor)
                                (طابق {{ $maintenance->unit->floor }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">المُبلِّغ</td>
                        <td>{{ $maintenance->reported_by ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">وصف المشكلة</td>
                        <td>{{ $maintenance->description }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">الحالة</td>
                        <td>
                            @php
                                $statusMap = ['pending'=>['label'=>'معلق','class'=>'bg-secondary'],
                                              'in_progress'=>['label'=>'جاري التنفيذ','class'=>'bg-primary'],
                                              'completed'=>['label'=>'مكتمل','class'=>'bg-success'],
                                              'cancelled'=>['label'=>'ملغي','class'=>'bg-danger']];
                                $st = $statusMap[$maintenance->status] ?? ['label'=>$maintenance->status,'class'=>'bg-secondary'];
                            @endphp
                            <span class="badge {{ $st['class'] }} rounded-pill px-3">{{ $st['label'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">التكلفة</td>
                        <td class="fw-semibold text-success">
                            {{ $maintenance->cost ? number_format($maintenance->cost) . ' ج.م' : '—' }}
                        </td>
                    </tr>
                    @if($maintenance->assignedTo)
                    <tr>
                        <td class="text-muted">مُسنَد إلى</td>
                        <td>{{ $maintenance->assignedTo->name }}</td>
                    </tr>
                    @endif
                    @if($maintenance->started_at)
                    <tr>
                        <td class="text-muted">تاريخ البدء</td>
                        <td>{{ \Carbon\Carbon::parse($maintenance->started_at)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                    @if($maintenance->finished_at)
                    <tr>
                        <td class="text-muted">تاريخ الانتهاء</td>
                        <td>{{ \Carbon\Carbon::parse($maintenance->finished_at)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">تاريخ الطلب</td>
                        <td>{{ $maintenance->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @if($maintenance->contract?->tenant)
                    <tr>
                        <td class="text-muted">المستأجر المرتبط</td>
                        <td>
                            <a href="{{ route('tenants.show', $maintenance->contract->tenant) }}">
                                {{ $maintenance->contract->tenant->name }}
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Quick update status card --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header fw-bold"><i class="bi bi-arrow-repeat me-2 text-info"></i>تحديث سريع للحالة</div>
            <div class="card-body">
                <form method="POST" action="{{ route('maintenance.update', $maintenance) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الحالة الحالية</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $maintenance->status==='pending'?'selected':'' }}>معلق — قيد الانتظار</option>
                            <option value="in_progress" {{ $maintenance->status==='in_progress'?'selected':'' }}>جاري التنفيذ</option>
                            <option value="completed" {{ $maintenance->status==='completed'?'selected':'' }}>مكتمل</option>
                            <option value="cancelled" {{ $maintenance->status==='cancelled'?'selected':'' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">التكلفة الفعلية (ج.م)</label>
                        <div class="input-group">
                            <span class="input-group-text">ج.م</span>
                            <input type="number" name="cost" class="form-control" value="{{ $maintenance->cost }}" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات / وصف</label>
                        <textarea name="description" class="form-control" rows="2" required>{{ $maintenance->description }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-lg me-1"></i>حفظ التحديث
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
