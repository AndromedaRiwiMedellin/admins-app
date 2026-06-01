@extends('layouts.admin')
@section('title', 'Métricas')

@section('topbar-actions')
    <form method="GET" action="{{ route('metrics.index') }}" class="flex items-center gap-2">
        <select
            name="event_id"
            class="bg-white border border-gray-200 text-gray-600 text-xs rounded-lg px-3 py-1.5 outline-none focus:border-[#009990] transition max-w-[220px]"
        >
            <option value="all" {{ $selectedEventId === 'all' ? 'selected' : '' }}>Todos los eventos</option>
            @foreach($eventOptions as $eventOption)
                <option value="{{ $eventOption->id }}" {{ (string) $selectedEventId === (string) $eventOption->id ? 'selected' : '' }}>
                    {{ $eventOption->title }}
                </option>
            @endforeach
        </select>
        <input
            type="date" name="from" value="{{ $from }}"
            class="bg-white border border-gray-200 text-gray-600 text-xs rounded-lg px-3 py-1.5 outline-none focus:border-[#009990] transition"
        >
        <span class="text-xs text-gray-400">—</span>
        <input
            type="date" name="to" value="{{ $to }}"
            class="bg-white border border-gray-200 text-gray-600 text-xs rounded-lg px-3 py-1.5 outline-none focus:border-[#009990] transition"
        >
        <button type="submit"
            class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-xs font-medium px-3 py-1.5 rounded-lg transition">
            Aplicar
        </button>
    </form>
    <a href="{{ route('metrics.export', ['from' => $from, 'to' => $to, 'event_id' => $selectedEventId]) }}"
       class="bg-white hover:bg-gray-50 text-[#001A6E] border border-white/70 text-xs font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
        Excel
    </a>
@endsection

@section('content')

{{-- Stats principales --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="bg-white border border-gray-100 border-l-4 border-l-[#009990] rounded-xl p-5">
        <p class="text-xs text-gray-400 mb-2">Boletas vendidas</p>
        <p class="text-3xl font-medium text-[#001A6E]">{{ $ticketsSold }}</p>
        <p class="text-xs text-gray-400 mt-1">En el rango seleccionado</p>
    </div>

    <div class="bg-white border border-gray-100 border-l-4 border-l-[#074799] rounded-xl p-5">
        <p class="text-xs text-gray-400 mb-2">Usuarios nuevos</p>
        <p class="text-3xl font-medium text-[#001A6E]">{{ $newUsers }}</p>
        <p class="text-xs text-gray-400 mt-1">Total: {{ $totalUsers }} registrados</p>
    </div>

    <div class="bg-white border border-gray-100 border-l-4 border-l-amber-400 rounded-xl p-5">
        <p class="text-xs text-gray-400 mb-2">Ingresos vendidos</p>
        <p class="text-3xl font-medium text-[#001A6E]">${{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Segun precio de la localidad</p>
    </div>

    <div class="bg-white border border-gray-100 border-l-4 border-l-[#E1FFBB] rounded-xl p-5">
        <p class="text-xs text-gray-400 mb-2">Ocupación promedio</p>
        <p class="text-3xl font-medium {{ $avgOccupancy >= 80 ? 'text-[#009990]' : ($avgOccupancy >= 50 ? 'text-amber-500' : 'text-gray-800') }}">
            {{ number_format($avgOccupancy, 1) }}%
        </p>
        <p class="text-xs text-gray-400 mt-1">Promedio por evento</p>
    </div>

</div>

<div class="bg-white border border-gray-100 rounded-xl overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-medium text-gray-800">Metricas por evento</h3>
            <p class="text-xs text-gray-400">Imagen, ventas, ingresos y ocupacion del rango seleccionado</p>
        </div>
        <span class="text-xs text-gray-400">{{ $eventMetrics->count() }} evento(s)</span>
    </div>

    @if($eventMetrics->isEmpty())
        <p class="text-sm text-gray-400 text-center py-8">Sin eventos para mostrar</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
            @foreach($eventMetrics as $item)
                @php
                    $poster = $item['poster_url'] ?? null;
                    $posterUrl = $poster
                        ? (\Illuminate\Support\Str::startsWith($poster, ['http://', 'https://']) ? $poster : asset('storage/' . $poster))
                        : null;
                @endphp
                <article class="border border-gray-100 rounded-xl overflow-hidden bg-gray-50">
                    <div class="h-36 bg-[#001A6E]/10 overflow-hidden">
                        @if($posterUrl)
                            <img src="{{ $posterUrl }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#001A6E] text-3xl font-medium">
                                {{ strtoupper(substr($item['title'], 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-gray-400 mb-1">{{ $item['date'] ?: 'Fecha por confirmar' }}</p>
                        <h4 class="text-sm font-medium text-gray-800 mb-3 truncate" title="{{ $item['title'] }}">{{ $item['title'] }}</h4>

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-white rounded-lg p-2 border border-gray-100">
                                <p class="text-[10px] text-gray-400">Vendidas</p>
                                <strong class="text-sm text-[#001A6E]">{{ $item['sold'] }}</strong>
                            </div>
                            <div class="bg-white rounded-lg p-2 border border-gray-100">
                                <p class="text-[10px] text-gray-400">Ingresos</p>
                                <strong class="text-sm text-[#001A6E]">${{ number_format($item['revenue'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="bg-white rounded-lg p-2 border border-gray-100">
                                <p class="text-[10px] text-gray-400">Capacidad</p>
                                <strong class="text-sm text-[#001A6E]">{{ $item['capacity'] ?? 0 }}</strong>
                            </div>
                            <div class="bg-white rounded-lg p-2 border border-gray-100">
                                <p class="text-[10px] text-gray-400">Ocupacion</p>
                                <strong class="text-sm {{ $item['occupancy'] >= 80 ? 'text-[#009990]' : ($item['occupancy'] >= 50 ? 'text-amber-500' : 'text-gray-600') }}">{{ $item['occupancy'] }}%</strong>
                            </div>
                        </div>

                        <div class="w-full h-2 bg-white rounded-full overflow-hidden">
                            <div class="h-2 rounded-full {{ $item['occupancy'] >= 80 ? 'bg-[#009990]' : ($item['occupancy'] >= 50 ? 'bg-amber-400' : 'bg-gray-300') }}"
                                 style="width: {{ min($item['occupancy'], 100) }}%"></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

    {{-- Gráfica boletas por semana --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#009990]/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-[#009990]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-800">Boletas por semana</h3>
                <p class="text-xs text-gray-400">Ventas en el rango seleccionado</p>
            </div>
        </div>
        <div class="p-5">
            @if($ticketsByWeek->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin datos en este rango</p>
            @else
                <canvas id="ticketsChart" height="200"></canvas>
            @endif
        </div>
    </div>

    {{-- Ocupación por evento --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#074799]/08 flex items-center justify-center">
                <svg class="w-4 h-4 text-[#074799]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-800">Ocupación por evento</h3>
                <p class="text-xs text-gray-400">% de llenado en el rango</p>
            </div>
        </div>
        <div class="p-5">
            @if($occupancyData->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Sin eventos en este rango</p>
            @else
                <div class="mb-5">
                    <canvas id="occupancyChart" height="160"></canvas>
                </div>
                @foreach($occupancyData as $item)
                    <div class="mb-4 last:mb-0">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs text-gray-700 truncate max-w-[200px]">{{ $item['title'] }}</span>
                            <span class="text-xs font-medium {{ $item['occupancy'] >= 80 ? 'text-[#009990]' : ($item['occupancy'] >= 50 ? 'text-amber-500' : 'text-gray-500') }}">
                                {{ $item['occupancy'] }}%
                            </span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500
                                {{ $item['occupancy'] >= 80 ? 'bg-[#009990]' : ($item['occupancy'] >= 50 ? 'bg-amber-400' : 'bg-gray-300') }}"
                                style="width: {{ $item['occupancy'] }}%">
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $item['sold'] }} / {{ $item['capacity'] }} boletas</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(!$ticketsByWeek->isEmpty())
const ctx = document.getElementById('ticketsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($ticketsByWeek->pluck('week')) !!},
        datasets: [{
            label: 'Boletas vendidas',
            data: {!! json_encode($ticketsByWeek->pluck('total')) !!},
            backgroundColor: 'rgba(0, 153, 144, 0.15)',
            borderColor: '#009990',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { color: '#9ca3af', font: { size: 11 } }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#9ca3af', font: { size: 11 } }
            }
        }
    }
});
@endif

@if(!$occupancyData->isEmpty())
const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
new Chart(occupancyCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($occupancyData->pluck('title')) !!},
        datasets: [{
            label: 'Ocupacion',
            data: {!! json_encode($occupancyData->pluck('occupancy')) !!},
            backgroundColor: 'rgba(7, 71, 153, 0.14)',
            borderColor: '#074799',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (context) => `${context.parsed.x}% de ocupacion`
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                max: 100,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { color: '#9ca3af', font: { size: 11 }, callback: (value) => `${value}%` }
            },
            y: {
                grid: { display: false },
                ticks: { color: '#9ca3af', font: { size: 11 } }
            }
        }
    }
});
@endif
</script>
@endpush
