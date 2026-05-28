<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Orbix</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#009990">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-[220px] bg-gray-50 border-r border-gray-200 flex flex-col z-50 transition-transform duration-300 md:translate-x-0 -translate-x-full">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-200">
        <div class="w-8 h-8 bg-[#009990] rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 56 56" fill="none">
                <polygon points="28,4 46,14 46,38 28,48 10,38 10,14" fill="#001A6E" opacity="0.3"/>
                <polygon points="28,10 42,18 42,34 28,42 14,34 14,18" fill="#E1FFBB" opacity="0.7"/>
                <polygon points="28,18 35,22 35,32 28,36 21,32 21,22" fill="none" stroke="#E1FFBB" stroke-width="2"/>
                <circle cx="28" cy="27" r="4" fill="#E1FFBB"/>
                <path d="M37,8 L48,13 L48,25 C48,35 42,41 37,44 C32,41 26,35 26,25 L26,13 Z" fill="#009990" stroke="#E1FFBB" stroke-width="1.5"/>
                <text x="37" y="30" text-anchor="middle" font-family="system-ui" font-size="10" font-weight="700" fill="#E1FFBB">A</text>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-[#001A6E] leading-none">Orbix</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Administración</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-3">
        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-2 pb-1">General</p>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('dashboard') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Contenido</p>
        <a href="{{ route('events.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('events.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Eventos
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Operaciones</p>
        <a href="{{ route('pqrs.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('pqrs.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            PQRS
            @php $pendingCount = \App\Models\Pqrs::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="ml-auto text-[10px] font-medium bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('employees.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('employees.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Empleados
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Análisis</p>
        <a href="{{ route('metrics.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('metrics.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            Métricas
        </a>
    </nav>

    {{-- Usuario --}}
    <div class="px-4 py-3 border-t border-gray-200">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#009990] flex items-center justify-center text-[11px] font-medium text-[#E1FFBB] flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->email, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-700 truncate">{{ auth()->user()->full_name ?? auth()->user()->email }}</p>
                <p class="text-[10px] text-gray-400">Administrador</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition p-1 rounded" title="Cerrar sesión">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Main --}}
<div class="md:ml-[220px] flex-1 flex flex-col min-w-0">

    {{-- Header teal --}}
    <header class="bg-[#009990] h-[50px] flex items-center px-5 gap-3 sticky top-0 z-40">
        <button class="md:hidden text-[#E1FFBB]" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="text-sm font-medium text-[#E1FFBB]">@yield('title', 'Dashboard')</span>
        <div class="ml-auto flex items-center gap-3">
            @yield('topbar-actions')
        </div>
    </header>

    {{-- Contenido --}}
    <main class="p-6 flex-1">
        @if(session('success'))
            <div class="flex items-center gap-2 bg-[#009990]/08 border border-[#009990]/20 text-[#007a74] text-sm rounded-lg px-4 py-2.5 mb-5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                {{ session('error') }}
            </div>
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
    if (window.innerWidth < 768 && !sidebar.contains(e.target) && !e.target.closest('button[onclick]')) {
        sidebar.classList.add('-translate-x-full');
    }
});
</script>

@stack('scripts')
</body>
</html<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Orbix</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#009990">
    <link rel="apple-touch-icon" href="/images/icon-192.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-[220px] bg-gray-50 border-r border-gray-200 flex flex-col z-50 transition-transform duration-300 md:translate-x-0 -translate-x-full">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-200">
        <div class="w-8 h-8 bg-[#009990] rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" viewBox="0 0 56 56" fill="none">
                <polygon points="28,4 46,14 46,38 28,48 10,38 10,14" fill="#001A6E" opacity="0.3"/>
                <polygon points="28,10 42,18 42,34 28,42 14,34 14,18" fill="#E1FFBB" opacity="0.7"/>
                <polygon points="28,18 35,22 35,32 28,36 21,32 21,22" fill="none" stroke="#E1FFBB" stroke-width="2"/>
                <circle cx="28" cy="27" r="4" fill="#E1FFBB"/>
                <path d="M37,8 L48,13 L48,25 C48,35 42,41 37,44 C32,41 26,35 26,25 L26,13 Z" fill="#009990" stroke="#E1FFBB" stroke-width="1.5"/>
                <text x="37" y="30" text-anchor="middle" font-family="system-ui" font-size="10" font-weight="700" fill="#E1FFBB">A</text>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-[#001A6E] leading-none">Orbix</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Administración</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-3">
        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-2 pb-1">General</p>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('dashboard') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Contenido</p>
        <a href="{{ route('events.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('events.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Eventos
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Operaciones</p>
        <a href="{{ route('pqrs.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('pqrs.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            PQRS
            @php $pendingCount = \App\Models\Pqrs::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="ml-auto text-[10px] font-medium bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('employees.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('employees.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Empleados
        </a>

        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest px-4 pt-4 pb-1">Análisis</p>
        <a href="{{ route('metrics.index') }}"
           class="flex items-center gap-2.5 mx-2 px-3 py-2 rounded-lg text-sm transition
                  {{ request()->routeIs('metrics.*') ? 'bg-[#009990]/10 text-[#009990] font-medium border-l-2 border-[#009990]' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            Métricas
        </a>
    </nav>

    {{-- Usuario --}}
    <div class="px-4 py-3 border-t border-gray-200">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-[#009990] flex items-center justify-center text-[11px] font-medium text-[#E1FFBB] flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->email, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-700 truncate">{{ auth()->user()->full_name ?? auth()->user()->email }}</p>
                <p class="text-[10px] text-gray-400">Administrador</p>
            </div>

            {{-- Logout sin form para no interferir con otros forms --}}
            <a href="#"
               onclick="event.preventDefault(); if(confirm('¿Cerrar sesión?')) document.getElementById('logout-form').submit();"
               class="text-gray-400 hover:text-red-400 transition p-1 rounded" title="Cerrar sesión">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </a>
        </div>
    </div>
</aside>

{{-- Form de logout fuera del sidebar --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

{{-- Main --}}
<div class="md:ml-[220px] flex-1 flex flex-col min-w-0">

    {{-- Header teal --}}
    <header class="bg-[#009990] h-[50px] flex items-center px-5 gap-3 sticky top-0 z-40">
        <button class="md:hidden text-[#E1FFBB]" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="text-sm font-medium text-[#E1FFBB]">@yield('title', 'Dashboard')</span>
        <div class="ml-auto flex items-center gap-3">
            @yield('topbar-actions')
        </div>
    </header>

    {{-- Contenido --}}
    <main class="p-6 flex-1">
        @if(session('success'))
            <div class="flex items-center gap-2 bg-[#009990]/08 border border-[#009990]/20 text-[#007a74] text-sm rounded-lg px-4 py-2.5 mb-5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                {{ session('error') }}
            </div>
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
    if (window.innerWidth < 768 && !sidebar.contains(e.target) && !e.target.closest('button[onclick]')) {
        sidebar.classList.add('-translate-x-full');
    }
});
</script>

@stack('scripts')
</body>
</html>>