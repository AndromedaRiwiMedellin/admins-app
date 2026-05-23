@extends('layouts.admin')
@section('title', 'Dashboard')

@section('topbar-actions')
    <span class="text-sm text-gray-400">{{ now()->format('d M Y') }}</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-3 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Eventos este mes</p>
        <p class="text-2xl font-medium text-[#001A6E]">{{ $stats['events_this_month'] }}</p>
        <p class="text-xs text-gray-400 mt-1">En cartelera</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Boletas esta semana</p>
        <p class="text-2xl font-medium text-[#001A6E]">{{ $stats['tickets_this_week'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Online + taquilla</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">PQRS pendientes</p>
        <p class="text-2xl font-medium {{ $stats['pending_pqrs'] > 0 ? 'text-amber-500' : 'text-[#009990]' }}">
            {{ $stats['pending_pqrs'] }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Sin respuesta</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Usuarios registrados</p>
        <p class="text-2xl font-medium text-[#001A6E]">{{ $stats['total_users'] }}</p>
        <p class="text-xs text-gray-400 mt-1">Total acumulado</p>
    </div>
</div>

{{-- Contenido principal --}}
<div class="grid grid-cols-2 gap-4">

    {{-- Próximos eventos --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-medium text-gray-800">Próximos eventos</h2>
            <a href="{{ route('events.create') }}"
               class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-xs font-medium px-3 py-1.5 rounded-lg transition">
                + Nuevo
            </a>
        </div>

        @forelse($upcomingEvents as $event)
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                <div class="bg-[#009990]/10 rounded-lg px-3 py-2 text-center min-w-[52px]">
                    <div class="text-lg font-medium text-[#009990]">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                    </div>
                    <div class="text-[10px] text-gray-400 uppercase">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('M') }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->event_date)->format('h:i A') }}</p>
                </div>
                <a href="{{ route('events.edit', $event) }}"
                   class="text-xs text-[#074799] hover:text-[#001A6E] border border-[#074799]/20 hover:border-[#001A6E]/30 px-2.5 py-1 rounded-lg transition">
                    Editar
                </a>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-6">No hay eventos próximos</p>
        @endforelse
    </div>

    {{-- PQRS pendientes --}}
    <div class="bg-white border border-gray-100 rounded-xl p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-medium text-gray-800">PQRS sin responder</h2>
            <a href="{{ route('pqrs.index') }}" class="text-xs text-[#009990] hover:underline">Ver todas →</a>
        </div>

        @forelse($recentPqrs as $pqrs)
            <div class="py-2.5 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-100 px-2 py-0.5 rounded-full">
                        {{ $pqrs->type }}
                    </span>
                    <span class="text-sm font-medium text-gray-800 truncate">
                        {{ \Illuminate\Support\Str::limit($pqrs->subject, 40) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-400">
                        {{ $pqrs->user?->full_name ?? $pqrs->user?->email ?? 'Usuario desconocido' }}
                    </span>
                    <a href="{{ route('pqrs.show', $pqrs) }}"
                       class="text-xs text-[#074799] hover:text-[#001A6E] border border-[#074799]/20 hover:border-[#001A6E]/30 px-2.5 py-1 rounded-lg transition">
                        Responder
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-center py-6" style="color:#009990">✓ Todo al día</p>
        @endforelse
    </div>

</div>

@endsection