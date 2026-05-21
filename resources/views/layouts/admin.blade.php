<<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Orbix</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#001A6E">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700&display=swap" rel="stylesheet">

    @livewireStyles

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #001A6E;
            --bg2:     #074799;
            --bg3:     #0a5ab8;
            --border:  rgba(255,255,255,0.10);
            --accent:  #009990;
            --accent2: #00c4b8;
            --lime:    #E1FFBB;
            --text:    #f0f4ff;
            --muted:   rgba(255,255,255,0.55);
            --danger:  #ff6b6b;
            --warning: #ffd166;
            --sidebar-w: 240px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: rgba(0,0,0,0.25);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo .logo-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
        }

        .sidebar-logo .logo-text h1 {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .sidebar-logo .logo-text span {
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }

        .nav-section {
            padding: 8px 16px 4px;
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            margin: 1px 8px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            transition: all .15s ease;
            position: relative;
        }

        .nav-item:hover { background: rgba(255,255,255,0.08); color: var(--text); }

        .nav-item.active {
            background: rgba(0,153,144,0.20);
            color: var(--accent2);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 18px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 10px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .user-card { display: flex; align-items: center; gap: 10px; }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(0,153,144,0.30);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600;
            color: var(--accent2);
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 11px; color: var(--muted); }

        .logout-btn {
            background: none; border: none;
            color: var(--muted); cursor: pointer;
            font-size: 18px; padding: 4px;
            border-radius: 6px;
            transition: color .15s;
        }
        .logout-btn:hover { color: var(--danger); }

        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }

        .topbar {
            background: rgba(0,0,0,0.20);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; }
        .topbar-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer; text-decoration: none; border: none;
            transition: all .15s ease;
        }
        .btn-primary { background: var(--bg2); color: #fff; }
        .btn-primary:hover { background: var(--bg3); }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { background: var(--accent2); color: #001A6E; }
        .btn-ghost { background: rgba(255,255,255,0.08); color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { background: rgba(255,255,255,0.12); color: var(--text); }
        .btn-danger { background: rgba(255,107,107,0.15); color: var(--danger); border: 1px solid rgba(255,107,107,0.3); }
        .btn-danger:hover { background: rgba(255,107,107,0.25); }

        .content { padding: 28px; flex: 1; }

        .flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .flash-success { background: rgba(225,255,187,0.12); border: 1px solid rgba(225,255,187,0.25); color: var(--lime); }
        .flash-error   { background: rgba(255,107,107,0.10); border: 1px solid rgba(255,107,107,0.25); color: var(--danger); }

        .card { background: rgba(0,0,0,0.20); border: 1px solid var(--border); border-radius: 12px; padding: 20px; }

        .stat-card { background: rgba(0,0,0,0.20); border: 1px solid var(--border); border-radius: 12px; padding: 20px; }
        .stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .stat-value { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 700; margin-top: 6px; }
        .stat-sub   { font-size: 12px; color: var(--muted); margin-top: 4px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th { text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
        tbody td { padding: 12px 14px; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(255,255,255,0.03); }

        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: rgba(225,255,187,0.15); color: var(--lime); }
        .badge-danger  { background: rgba(255,107,107,0.12); color: var(--danger); }
        .badge-warning { background: rgba(255,209,102,0.12); color: var(--warning); }
        .badge-accent  { background: rgba(0,153,144,0.15);   color: var(--accent2); }
        .badge-muted   { background: rgba(255,255,255,0.08); color: var(--muted); }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--muted); margin-bottom: 6px; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: rgba(0,0,0,0.25);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .15s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--accent); }
        .form-textarea { resize: vertical; min-height: 100px; }
        .form-select option { background: #001A6E; }
        .form-error { font-size: 12px; color: var(--danger); margin-top: 4px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

        /* Mobile */
        .menu-toggle { display: none; background: none; border: none; color: var(--text); font-size: 22px; cursor: pointer; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .menu-toggle { display: block; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <svg class="logo-icon" viewBox="0 0 36 36" fill="none">
            <polygon points="18,4 30,11 30,25 18,32 6,25 6,11" fill="#074799" opacity="0.3"/>
            <polygon points="18,8 27,13 27,23 18,28 9,23 9,13" fill="#009990" opacity="0.7"/>
            <polygon points="18,13 23,16 23,22 18,25 13,22 13,16" fill="none" stroke="#009990" stroke-width="1.5"/>
            <circle cx="18" cy="19" r="3" fill="#009990"/>
            <path d="M24,6 L31,9 L31,17 C31,23 27,27 24,29 C21,27 17,23 17,17 L17,9 Z" fill="#001A6E" stroke="#E1FFBB" stroke-width="1"/>
            <text x="24" y="21" text-anchor="middle" font-family="system-ui" font-size="7" font-weight="700" fill="#E1FFBB">A</text>
        </svg>
        <div class="logo-text">
            <h1>Orbix</h1>
            <span>Administración</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">General</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            ▣ Dashboard
        </a>

        <div class="nav-section">Contenido</div>
        <a href="{{ route('events.index') }}" class="nav-item {{ request()->routeIs('events.*') ? 'active' : '' }}">
            ◈ Eventos
        </a>

        <div class="nav-section">Operaciones</div>
        <a href="{{ route('pqrs.index') }}" class="nav-item {{ request()->routeIs('pqrs.*') ? 'active' : '' }}">
            ◉ PQRS
            @php $pendingCount = \App\Models\Pqrs::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
            ○ Empleados
        </a>

        <div class="nav-section">Análisis</div>
        <a href="{{ route('metrics.index') }}" class="nav-item {{ request()->routeIs('metrics.*') ? 'active' : '' }}">
            ▲ Métricas
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->email, 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->full_name ?? auth()->user()->email }}</div>
                <div class="user-role">Administrador</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Cerrar sesión">→</button>
            </form>
        </div>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
        <span class="topbar-title">@yield('title', 'Dashboard')</span>
        <div class="topbar-actions">
            @yield('topbar-actions')
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="flash flash-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">⚠ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

@livewireScripts

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(console.error);
}
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !e.target.closest('.menu-toggle')) {
        sidebar.classList.remove('open');
    }
});
</script>

@stack('scripts')
</body>
</html>