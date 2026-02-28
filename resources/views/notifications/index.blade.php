@extends('layouts.app')
@section('title', 'الإشعارات')
@section('page-title', 'الإشعارات')
@section('content')
<div class="d-flex justify-content-end mb-3">
    @if($notifications->where('is_read', false)->count() > 0)
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
        @csrf
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-check-all me-1"></i>تحديد الكل كمقروء</button>
    </form>
    @endif
</div>
<div class="card">
    @forelse($notifications as $n)
    <div class="d-flex align-items-start p-3 border-bottom {{ $n->is_read ? '' : 'bg-light' }}">
        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;min-width:40px;background:{{ $n->is_read ? '#e4e6ea' : '#dbeafe' }};color:{{ $n->is_read ? '#888' : '#1a5276' }};">
            <i class="bi bi-bell-fill"></i>
        </div>
        <div class="flex-grow-1">
            <div class="{{ $n->is_read ? 'text-muted' : 'fw-semibold' }}">{{ $n->message }}</div>
            <div class="text-muted small">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <div class="d-flex gap-1 align-items-center ms-3">
            @if($n->link)
            <a href="{{ $n->link }}" class="btn btn-xs btn-sm btn-outline-primary py-0 px-2">عرض</a>
            @endif
            @if(!$n->is_read)
            <form method="POST" action="{{ route('notifications.mark-read', $n) }}">@csrf @method('PATCH')
                <button class="btn btn-xs btn-sm btn-outline-secondary py-0 px-2"><i class="bi bi-check"></i></button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center text-muted py-5">
        <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>لا توجد إشعارات
    </div>
    @endforelse
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
