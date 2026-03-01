@extends('layouts.app')
@section('title', 'سجل الأحداث')
@section('page-title', 'سجل الأحداث')
@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>الإجراء</th><th>النموذج</th><th>المستخدم</th><th>التغييرات</th><th>IP</th><th>التاريخ</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="badge bg-secondary rounded-pill">{{ $log->action }}</span></td>
                    <td>
                        @if($log->model_type)
                            <small class="text-muted">{{ class_basename($log->model_type) }}</small>
                            @if($log->model_id) <span class="badge bg-light text-dark">#{{ $log->model_id }}</span> @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $log->user?->name ?? 'النظام' }}</td>
                    <td>
                        @if($log->changes)
                            <small class="text-muted" style="font-size:0.75rem;word-break:break-all;">
                                {{ Str::limit(json_encode($log->changes, JSON_UNESCAPED_UNICODE), 120) }}
                            </small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $log->ip_address ?? '—' }}</td>
                    <td class="text-muted small">{{ $log->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">لا توجد سجلات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection
