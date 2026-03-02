<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة الإيجارات')</title>

    <!-- Google Fonts: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:       #1a5276;
            --primary-light: #2980b9;
            --sidebar-bg:    #0f2a45;
            --sidebar-text:  #d0e8f5;
            --sidebar-active:#2980b9;
            --bg-main:       #f0f4f8;
        }
        * { font-family: 'Cairo', sans-serif; }
        body { background: var(--bg-main); min-height: 100vh; }

        /* Sidebar */
        #sidebar {
            width: 260px; min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed; top: 0; right: 0;
            z-index: 1000; box-shadow: -4px 0 20px rgba(0,0,0,.3);
        }
        #sidebar .brand {
            background: linear-gradient(135deg, #1a5276, #2980b9);
            padding: 1.2rem 1rem; color: #fff; text-align: center;
        }
        #sidebar .brand h5 { margin: 0; font-weight: 900; font-size: 1.05rem; }
        #sidebar .nav-link {
            color: var(--sidebar-text); padding: .6rem 1.2rem;
            border-radius: 8px; margin: 2px 8px;
            transition: all .2s; font-weight: 600; font-size: .87rem;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background: var(--sidebar-active); color: #fff;
        }
        #sidebar .nav-link i { margin-left: .5rem; }
        .nav-section {
            font-size: .68rem; color: rgba(255,255,255,.4);
            padding: .6rem 1.5rem .2rem; text-transform: uppercase; letter-spacing: 1px;
        }

        /* Main */
        #main { margin-right: 260px; min-height: 100vh; }

        /* Topbar */
        #topbar {
            background: #fff; height: 62px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            display: flex; align-items: center;
            padding: 0 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        #topbar .page-title { font-weight: 700; color: var(--primary); font-size: 1.1rem; }

        /* Cards */
        .stat-card {
            border-radius: 16px; border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,.13); }
        .icon-box {
            width: 58px; height: 58px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }

        /* Tables */
        .table-custom { border-radius: 12px; overflow: hidden; white-space: nowrap; }
        .table-custom thead th {
            background: var(--primary); color: #fff; font-weight: 700; border: none;
        }
        .table-custom tbody tr:hover { background: #e8f4fd; }

        /* Sidebar Overlay for Mobile */
        #sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 999; backdrop-filter: blur(2px);
            opacity: 0; transition: opacity 0.3s;
        }
        #sidebar-overlay.show { display: block; opacity: 1; }

        /* Badges */
        .badge-active      { background:#d5f5e3; color:#1e8449; }
        .badge-expired     { background:#fde8e8; color:#c0392b; }
        .badge-terminated  { background:#f0f0f0; color:#555; }
        .badge-vacant      { background:#fff3cd; color:#856404; }
        .badge-rented      { background:#d5f5e3; color:#1e8449; }
        .badge-maintenance { background:#fde8e8; color:#c0392b; }
        .badge-due         { background:#d1ecf1; color:#0c5460; }
        .badge-paid        { background:#d5f5e3; color:#1e8449; }
        .badge-partial     { background:#fff3cd; color:#856404; }
        .badge-overdue     { background:#fde8e8; color:#c0392b; }
        .badge-pending     { background:#fff3cd; color:#856404; }
        .badge-in_progress { background:#d1ecf1; color:#0c5460; }
        .badge-completed   { background:#d5f5e3; color:#1e8449; }
        .badge-cancelled   { background:#f0f0f0; color:#555; }

        /* Forms */
        .form-control, .form-select {
            border-radius: 10px; padding: .6rem .9rem; transition: all .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(41,128,185,.15);
        }

        .page-content { padding: 1.5rem; }
        .card { border-radius: 14px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .card-header { background: transparent; border-bottom: 1px solid #e9ecef; font-weight: 700; padding: 1rem 1.25rem; }

        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(10px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-in { animation: fadeInUp .3s ease; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(260px); transition: transform .3s; }
            #sidebar.open { transform: translateX(0); }
            #main { margin-right: 0; }
            
            #topbar { padding: 0 0.8rem; }
            .page-title { font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
            .page-content { padding: 1rem 0.5rem; }
            .card-header { padding: 0.75rem 1rem; }
            
            /* Notification Dropdown Mobile Fix */
            .notif-dropdown-menu { width: 92vw !important; max-width: 360px !important; left: -60px !important; right: auto !important; }
            .dropdown-menu-start { left: 0 !important; right: auto !important; }
            
            /* Optimize Action Buttons in Tables */
            .table-custom td .btn { padding: 0.25rem 0.4rem; font-size: 0.75rem; }
            .icon-box { width: 48px; height: 48px; font-size: 1.25rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY -->
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="brand">
        <div style="font-size:2rem; margin-bottom:.2rem;">🏢</div>
        <h5>نظام الإيجارات الذكي</h5>
        <small style="opacity:.75; font-size:.75rem;">Smart Rental System</small>
    </div>
    <div class="py-2" style="overflow-y:auto; max-height:calc(100vh - 130px);">
        <div class="nav-section">الرئيسية</div>
        <a href="{{ route('dashboard') }}" class="nav-link @routeIs('dashboard')">
            <i class="bi bi-grid-fill"></i> لوحة التحكم
        </a>

        <div class="nav-section">إدارة العقارات</div>
        <a href="{{ route('buildings.index') }}" class="nav-link @routeIs('buildings.*')">
            <i class="bi bi-building-fill"></i> الأبراج والمباني
        </a>
        <a href="{{ route('units.index') }}" class="nav-link @routeIs('units.*')">
            <i class="bi bi-door-open-fill"></i> الوحدات
        </a>

        <div class="nav-section">الإيجارات</div>
        <a href="{{ route('tenants.index') }}" class="nav-link @routeIs('tenants.*')">
            <i class="bi bi-people-fill"></i> المستأجرون
        </a>
        <a href="{{ route('contracts.index') }}" class="nav-link @routeIs('contracts.*')">
            <i class="bi bi-file-earmark-text-fill"></i> العقود
        </a>
        <a href="{{ route('rent-schedules.index') }}" class="nav-link @routeIs('rent-schedules.*')">
            <i class="bi bi-calendar-check-fill"></i> استحقاقات الإيجار
        </a>

        <div class="nav-section">المالية</div>
        <a href="{{ route('payments.index') }}" class="nav-link @routeIs('payments.*')">
            <i class="bi bi-cash-coin"></i> المدفوعات
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link @routeIs('reports.*')">
            <i class="bi bi-bar-chart-fill"></i> التقارير
        </a>

        <div class="nav-section">الصيانة</div>
        <a href="{{ route('maintenance.index') }}" class="nav-link @routeIs('maintenance.*')">
            <i class="bi bi-tools"></i> طلبات الصيانة
        </a>

        <div class="nav-section">الإدارة</div>
        <a href="{{ route('users.index') }}" class="nav-link @routeIs('users.*')">
            <i class="bi bi-person-gear"></i> المستخدمون
        </a>
        <a href="{{ route('audit.index') }}" class="nav-link @routeIs('audit.*')">
            <i class="bi bi-journal-text"></i> سجل الأحداث
        </a>
    </div>
</nav>

<!-- MAIN WRAPPER -->
<div id="main">
    <!-- TOPBAR -->
    <div id="topbar">
        <button class="btn btn-sm d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list fs-4"></i>
        </button>
        <span class="page-title ms-2">@yield('page-title', 'لوحة التحكم')</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            @php
                $bellNotifs = \App\Models\AppNotification::where('user_id', auth()->id())
                    ->latest()->limit(8)->get();
                $bellUnread = $bellNotifs->where('is_read', false)->count();
            @endphp
            <div class="dropdown">
                <button class="btn btn-sm position-relative text-secondary border-0 bg-transparent p-0"
                        id="notifBell" data-bs-toggle="dropdown" aria-expanded="false"
                        style="font-size:1.25rem; line-height:1;">
                    <i class="bi bi-bell-fill"></i>
                    @if($bellUnread > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                        {{ $bellUnread > 99 ? '99+' : $bellUnread }}
                    </span>
                    @endif
                </button>
                <div class="dropdown-menu notif-dropdown-menu dropdown-menu-start shadow-lg p-0" style="width:360px;max-height:480px;overflow-y:auto;border-radius:14px;">
                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light" style="border-radius:14px 14px 0 0;">
                        <span class="fw-bold small"><i class="bi bi-bell me-1"></i>الإشعارات</span>
                        <div class="d-flex gap-2">
                            @if($bellUnread > 0)
                            <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-link text-primary p-0 small text-decoration-none" style="font-size:.78rem;">
                                    <i class="bi bi-check2-all me-1"></i>قراءة الكل
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('notifications.index') }}" class="text-muted small text-decoration-none" style="font-size:.78rem;">كل الإشعارات</a>
                        </div>
                    </div>
                    {{-- Notification items --}}
                    @forelse($bellNotifs as $notif)
                    @php
                        $p   = $notif->payload ?? [];
                        $url = $p['url'] ?? route('notifications.index');
                        $notifTypeIcon = match($notif->type ?? '') {
                            'rent_due_today'        => '📅',
                            'rent_overdue_reminder' => '⚠️',
                            'contract_ending_soon'  => '📋',
                            default                 => '🔔',
                        };
                    @endphp
                    <a href="{{ $url }}" class="d-block text-decoration-none px-3 py-2 border-bottom {{ $notif->is_read ? '' : 'bg-info bg-opacity-10' }}"
                       style="transition:.15s;">
                        <div class="d-flex gap-2 align-items-start">
                            <span style="font-size:1.3rem;line-height:1.4;">{{ $notifTypeIcon }}</span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small" style="font-size:.83rem;line-height:1.3;">
                                    {{ $p['title'] ?? $notif->type }}
                                    @if(!$notif->is_read)<span class="badge bg-danger ms-1" style="font-size:.6rem;">جديد</span>@endif
                                </div>
                                @if(isset($p['message']))
                                <div class="text-muted" style="font-size:.75rem;line-height:1.3;margin-top:2px;
                                     overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $p['message'] }}
                                </div>
                                @endif
                                <div class="text-muted mt-1" style="font-size:.7rem;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2 opacity-40"></i>
                        لا توجد إشعارات
                    </div>
                    @endforelse
                    {{-- Footer --}}
                    @if($bellNotifs->count() > 0)
                    <div class="text-center py-2 bg-light" style="border-radius:0 0 14px 14px;">
                        <a href="{{ route('notifications.index') }}" class="small text-primary text-decoration-none fw-semibold">
                            عرض كل الإشعارات <i class="bi bi-arrow-left ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-start shadow">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content">
        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto me-0" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto me-0" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <strong>يرجى تصحيح الأخطاء:</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close ms-auto me-0" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="fade-in">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
</script>
@stack('scripts')
</body>
</html>
