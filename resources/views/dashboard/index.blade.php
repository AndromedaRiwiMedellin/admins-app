@extends('layouts.admin')
@section('title', 'Dashboard')

@section('topbar-actions')
    <span style="font-size:13px; color:var(--muted);">{{ now()->format('d M Y') }}</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-label">Eventos este mes</div>
        <div class="stat-value">{{ $stats['events_this_month'] }}</div>
        <div class="stat-sub">En cartelera</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Boletas esta semana</div>
        <div class="stat-value">{{ $stats['tickets_this_week'] }}</div>
        <div class="stat-sub">Online + taquilla</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">PQRS pendientes</div>
        <div class="stat-value" style="color: {{ $stats['pending_pqrs'] > 0 ? 'var(--warning)' : 'var(--lime)' }}">
            {{ $stats['pending_pqrs'] }}
        </div>
        <div class="stat-sub">Sin respuesta</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Usuarios registrados</div>
        <div class="stat-value">{{ $stats['total_users'] }}</div>
        <div class="stat-sub">Total acumulado</div>
    </div>
</div>

{{-- Contenido principal --}}
<div class="grid-2">

    {{-- Próximos eventos --}}
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:15px; font-weight:600;">Próximos eventos</h2>
            <a href="{{ route('events.create') }}" class="btn btn-accent" style="padding:6px 12px; font-size:12px;">+ Nuevo</a>
        </div>

        @forelse($upcomingEvents as $event)
            <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--border);">
                <div style="background:rgba(0,153,144,0.12); border-radius:8px; padding:8px 12px; text-align:center; min-width:52px;">
                    <div style="font-size:18px; font-weight:700; color:var(--accent2); font-family:'Syne',sans-serif;">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                    </div>
                    <div style="font-size:10px; color:var(--muted); text-transform:uppercase;">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('M') }}
                    </div>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:14px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $event->title }}
                    </div>
                    <div style="font-size:12px; color:var(--muted);">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('h:i A') }}
                    </div>
                </div>
                <a href="{{ route('events.edit', $event) }}" class="btn btn-ghost" style="padding:5px 10px; font-size:12px;">Editar</a>
            </div>
        @empty
            <p style="color:var(--muted); font-size:14px; text-align:center; padding:24px 0;">
                No hay eventos próximos
            </p>
        @endforelse
    </div>

    {{-- PQRS pendientes --}}
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:15px; font-weight:600;">PQRS sin responder</h2>
            <a href="{{ route('pqrs.index') }}" style="font-size:12px; color:var(--accent2); text-decoration:none;">Ver todas →</a>
        </div>

        @forelse($recentPqrs as $pqrs)
            <div style="padding:10px 0; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                    <span class="badge badge-warning">{{ $pqrs->type }}</span>
                    <span style="font-size:13px; font-weight:500;">
                        {{ \Illuminate\Support\Str::limit($pqrs->subject, 40) }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12px; color:var(--muted);">
                        {{ $pqrs->user?->full_name ?? $pqrs->user?->email ?? 'Usuario desconocido' }}
                    </span>
                    <a href="{{ route('pqrs.show', $pqrs) }}" class="btn btn-ghost" style="padding:4px 10px; font-size:12px;">
                        Responder
                    </a>
                </div>
            </div>
        @empty
            <p style="color:var(--lime); font-size:14px; text-align:center; padding:24px 0;">
                ✓ Todo al día
            </p>
        @endforelse
    </div>

</div>

@endsection