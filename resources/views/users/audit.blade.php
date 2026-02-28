@extends('layouts.app')
@section('title', 'سجل الأحداث')
@section('page-title', 'سجل الأحداث')
@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
            <thead><tr><th>الحدث</th><th>السجل</th><th>المستخدم</th><th>بيانات</th><th>التاريخ</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="badge bg-secondary rounded-pill">{{ $log->event }}</span></td>
                    <td><small class="text-muted">{{ $log->log_name }}</small></td>
                    <td>{{ $log->causer?->name ?? 'النظام' }}</td>
                    <td><small>{{ Str::limit(json_encode($log->properties, JSON_UNESCAPED_UNICODE), 100) }}</small></td>
                    <td class="text-muted small">{{ $log->created_at->format('Y-m-d H:i') }}</td>
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
