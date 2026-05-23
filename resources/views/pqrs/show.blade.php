@extends('layouts.admin')
@section('title', 'PQRS — ' . $pqrs->subject)

@section('topbar-actions')
    <a href="{{ route('pqrs.index') }}"
       class="text-xs text-[#E1FFBB]/70 hover:text-[#E1FFBB] transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Volver
    </a>
@endsection

@section('content')

<div class="max-w-3xl mx-auto flex flex-col gap-4">

    {{-- Info de la solicitud --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $typeColors  = ['P'=>'bg-blue-50 text-blue-600 border-blue-100','Q'=>'bg-red-50 text-red-600 border-red-100','R'=>'bg-amber-50 text-amber-600 border-amber-100','S'=>'bg-green-50 text-green-600 border-green-100'];
                    $typeLabels  = ['P'=>'Pregunta','Q'=>'Queja','R'=>'Reclamo','S'=>'Sugerencia'];
                    $statusColors = ['pending'=>'bg-amber-50 text-amber-600 border-amber-100','in_process'=>'bg-blue-50 text-blue-600 border-blue-100','resolved'=>'bg-green-50 text-green-600 border-green-100'];
                    $statusLabels = ['pending'=>'Pendiente','in_process'=>'En gestión','resolved'=>'Resuelto'];
                @endphp
                <span class="text-[10px] font-medium border px-2 py-0.5 rounded-full {{ $typeColors[$pqrs->type] ?? '' }}">
                    {{ $typeLabels[$pqrs->type] ?? $pqrs->type }}
                </span>
                <span class="text-[10px] font-medium border px-2 py-0.5 rounded-full {{ $statusColors[$pqrs->status] ?? '' }}">
                    {{ $statusLabels[$pqrs->status] ?? $pqrs->status }}
                </span>
            </div>
            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pqrs->created_at)->format('d M Y, h:i A') }}</span>
        </div>

        <div class="p-6">
            <h2 class="text-base font-medium text-gray-800 mb-1">{{ $pqrs->subject }}</h2>
            <p class="text-xs text-gray-400 mb-4">
                De: <span class="text-gray-600">{{ $pqrs->user?->full_name ?? '—' }}</span>
                — {{ $pqrs->user?->email }}
            </p>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $pqrs->message }}</p>
        </div>
    </div>

    {{-- Historial de respuestas --}}
    @if($pqrs->responses->count() > 0)
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-medium text-gray-800">Historial de respuestas</h3>
            </div>
            <div class="p-6 flex flex-col gap-4">
                @foreach($pqrs->responses as $response)
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-full bg-[#009990] flex items-center justify-center text-[10px] font-medium text-[#E1FFBB] flex-shrink-0">
                            {{ strtoupper(substr($response->employee?->user?->full_name ?? 'A', 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-700">
                                    {{ $response->employee?->user?->full_name ?? 'Administrador' }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($response->created_at)->format('d M Y, h:i A') }}
                                </span>
                            </div>
                            <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-3">
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $response->response }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Formulario de respuesta --}}
    @if($pqrs->status !== 'resolved')
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#009990]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#009990]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-800">Responder solicitud</h3>
                    <p class="text-xs text-gray-400">El cliente recibirá un correo con tu respuesta</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pqrs.respond', $pqrs) }}" class="p-6 flex flex-col gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Respuesta</label>
                    <textarea
                        name="response"
                        rows="4"
                        placeholder="Escribe tu respuesta al cliente..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition resize-none {{ $errors->has('response') ? 'border-red-400' : '' }}"
                        required
                    >{{ old('response') }}</textarea>
                    @error('response') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Actualizar estado</label>
                    <select name="status"
                        class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition">
                        <option value="in_process" {{ $pqrs->status === 'in_process' ? 'selected' : '' }}>En gestión</option>
                        <option value="resolved">Marcar como resuelto</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('pqrs.index') }}"
                       class="text-sm text-gray-400 hover:text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-sm font-medium px-5 py-2 rounded-lg transition">
                        Enviar respuesta
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-green-50 border border-green-100 rounded-xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
            <p class="text-sm text-green-700">Esta solicitud ya fue marcada como resuelta.</p>
        </div>
    @endif

</div>

@endsection