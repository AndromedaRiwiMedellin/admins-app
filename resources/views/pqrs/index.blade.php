@extends('layouts.admin')
@section('title', 'PQRS')

@section('topbar-actions')
    <div class="flex items-center gap-2">
        <form method="GET" action="{{ route('pqrs.index') }}" class="flex items-center gap-2">
            <select name="type" onchange="this.form.submit()"
                class="bg-white border border-gray-200 text-gray-600 text-xs rounded-lg px-3 py-1.5 outline-none focus:border-[#009990] transition">
                <option value="">Todos los tipos</option>
                <option value="P" {{ request('type') === 'P' ? 'selected' : '' }}>Pregunta</option>
                <option value="Q" {{ request('type') === 'Q' ? 'selected' : '' }}>Queja</option>
                <option value="R" {{ request('type') === 'R' ? 'selected' : '' }}>Reclamo</option>
                <option value="S" {{ request('type') === 'S' ? 'selected' : '' }}>Sugerencia</option>
            </select>
            <select name="status" onchange="this.form.submit()"
                class="bg-white border border-gray-200 text-gray-600 text-xs rounded-lg px-3 py-1.5 outline-none focus:border-[#009990] transition">
                <option value="">Todos los estados</option>
                <option value="pending"    {{ request('status') === 'pending'    ? 'selected' : '' }}>Pendiente</option>
                <option value="in_process" {{ request('status') === 'in_process' ? 'selected' : '' }}>En gestión</option>
                <option value="resolved"   {{ request('status') === 'resolved'   ? 'selected' : '' }}>Resuelto</option>
            </select>
        </form>
    </div>
@endsection

@section('content')

<div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

    {{-- Buscador --}}
    <div class="px-5 py-3 border-b border-gray-100">
        <form method="GET" action="{{ route('pqrs.index') }}">
            <input type="hidden" name="type"   value="{{ request('type') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input
                type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Buscar por asunto o mensaje..."
                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition"
            >
        </form>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-400">Tipo</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-400">Asunto</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-400">Usuario</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-400">Estado</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-400">Fecha</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pqrs as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">

                        {{-- Tipo --}}
                        <td class="px-5 py-3">
                            @php
                                $typeColors = [
                                    'P' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Q' => 'bg-red-50 text-red-600 border-red-100',
                                    'R' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'S' => 'bg-green-50 text-green-600 border-green-100',
                                ];
                                $typeLabels = ['P'=>'Pregunta','Q'=>'Queja','R'=>'Reclamo','S'=>'Sugerencia'];
                            @endphp
                            <span class="text-[10px] font-medium border px-2 py-0.5 rounded-full {{ $typeColors[$item->type] ?? 'bg-gray-50 text-gray-500 border-gray-100' }}">
                                {{ $typeLabels[$item->type] ?? $item->type }}
                            </span>
                        </td>

                        {{-- Asunto --}}
                        <td class="px-5 py-3">
                            <p class="text-sm text-gray-800 font-medium">{{ \Illuminate\Support\Str::limit($item->subject, 45) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Str::limit($item->message, 50) }}</p>
                        </td>

                        {{-- Usuario --}}
                        <td class="px-5 py-3">
                            <p class="text-sm text-gray-700">{{ $item->user?->full_name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $item->user?->email ?? '' }}</p>
                        </td>

                        {{-- Estado --}}
                        <td class="px-5 py-3">
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'in_process' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'resolved'   => 'bg-green-50 text-green-600 border-green-100',
                                ];
                                $statusLabels = [
                                    'pending'    => 'Pendiente',
                                    'in_process' => 'En gestión',
                                    'resolved'   => 'Resuelto',
                                ];
                            @endphp
                            <span class="text-[10px] font-medium border px-2 py-0.5 rounded-full {{ $statusColors[$item->status] ?? 'bg-gray-50 text-gray-500 border-gray-100' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                        </td>

                        {{-- Fecha --}}
                        <td class="px-5 py-3 text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </td>

                        {{-- Acción --}}
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('pqrs.show', $item) }}"
                               class="text-xs text-[#074799] hover:text-[#001A6E] border border-[#074799]/20 hover:border-[#001A6E]/30 px-2.5 py-1 rounded-lg transition">
                                {{ $item->status === 'pending' ? 'Responder' : 'Ver' }}
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                            No hay solicitudes que coincidan con los filtros
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($pqrs->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $pqrs->withQueryString()->links() }}
        </div>
    @endif

</div>

@endsection