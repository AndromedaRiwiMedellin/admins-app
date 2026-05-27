@extends('layouts.admin')
@section('title', 'Eventos')

@section('topbar-actions')
    <a href="{{ route('events.create') }}"
       class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-xs font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo evento
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-3 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Total eventos</p>
        <p class="text-2xl font-medium text-[#001A6E]">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Registrados</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Activos</p>
        <p class="text-2xl font-medium text-[#009990]">{{ $stats['active'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Venta abierta</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Próximos</p>
        <p class="text-2xl font-medium text-[#074799]">{{ $stats['upcoming'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Por realizarse</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Finalizados</p>
        <p class="text-2xl font-medium text-gray-400">{{ $stats['past'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Realizados</p>
    </div>
</div>

{{-- Tabla --}}
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

    {{-- Filtros --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-medium text-gray-800">Lista de eventos</h2>
        <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-2">
            <select name="status"
                    class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 outline-none focus:border-[#009990] transition">
                <option value="">Todos los estados</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Activos</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Próximos</option>
                <option value="past"     {{ request('status') === 'past'     ? 'selected' : '' }}>Finalizados</option>
            </select>
            <input
                type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Buscar evento..."
                class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-700 outline-none focus:border-[#009990] w-48 transition"
            >
            <button type="submit"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg transition">
                Filtrar
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('events.index') }}" class="text-xs text-gray-400 hover:text-gray-600 transition">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- Eventos --}}
    @forelse($events as $event)
        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">

            {{-- Poster --}}
            <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                @if($event->poster_url)
                    <img src="{{ asset('storage/' . $event->poster_url) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-[#009990]/10">
                        <svg class="w-5 h-5 text-[#009990]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $event->title }}</p>
                <div class="flex items-center gap-3 mt-0.5">
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y · h:i A') }}
                    </span>
                </div>
            </div>

            {{-- Capacidad --}}
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-medium text-gray-800">{{ number_format($event->total_capacity) }}</p>
                <p class="text-xs text-gray-400">capacidad</p>
            </div>

            {{-- Ocupación --}}
            <div class="w-24 flex-shrink-0">
                @php
                    $sold = $event->tickets()->count();
                    $cap = $event->total_capacity > 0 ? $event->total_capacity : 1;
                    $pct = round(($sold / $cap) * 100);
                @endphp
                <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                    <span>{{ $sold }}/{{ $event->total_capacity }}</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-[#001A6E]' : 'bg-[#009990]' }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>

            {{-- Estado --}}
            <div class="flex-shrink-0">
                @php
                    $now = now();
                    if ($event->sale_start && $event->sale_end) {
                        if ($now->between($event->sale_start, $event->sale_end)) {
                            $status = 'active';
                        } elseif ($now->lt($event->sale_start)) {
                            $status = 'upcoming';
                        } else {
                            $status = 'past';
                        }
                    } else {
                        $status = 'upcoming';
                    }
                @endphp
                @switch($status)
                    @case('active')
                        <span class="text-[10px] font-medium bg-[#009990]/10 text-[#007a74] border border-[#009990]/20 px-2 py-0.5 rounded-full">Activo</span>
                        @break
                    @case('upcoming')
                        <span class="text-[10px] font-medium bg-[#074799]/08 text-[#074799] border border-[#074799]/20 px-2 py-0.5 rounded-full">Próximo</span>
                        @break
                    @case('past')
                        <span class="text-[10px] font-medium bg-gray-100 text-gray-400 border border-gray-200 px-2 py-0.5 rounded-full">Finalizado</span>
                        @break
                @endswitch
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('events.edit', $event) }}"
                   class="text-xs text-[#074799] hover:text-[#001A6E] border border-[#074799]/20 hover:border-[#001A6E]/30 px-2.5 py-1 rounded-lg transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('events.destroy', $event) }}"
                      onsubmit="return confirm('¿Eliminar {{ $event->title }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-xs text-red-400 hover:text-red-600 border border-red-100 hover:border-red-300 px-2.5 py-1 rounded-lg transition">
                        Eliminar
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <p class="text-sm text-gray-400">No hay eventos registrados</p>
            <a href="{{ route('events.create') }}" class="mt-3 text-xs text-[#009990] hover:underline">Crear el primero →</a>
        </div>
    @endforelse

    {{-- Paginación --}}
    @if($events->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $events->links() }}
        </div>
    @endif

</div>

@endsection