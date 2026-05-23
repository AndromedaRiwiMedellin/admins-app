@extends('layouts.admin')
@section('title', 'Nuevo evento')

@section('topbar-actions')
    <a href="{{ route('events.index') }}"
       class="text-xs text-[#E1FFBB]/70 hover:text-[#E1FFBB] transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Volver
    </a>
@endsection

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
        @csrf

        {{-- Info principal --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#009990]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#009990]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-800">Información general</h2>
                    <p class="text-xs text-gray-400">Datos principales del evento</p>
                </div>
            </div>

            <div class="p-6 flex flex-col gap-5">

                {{-- Título --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="title">Título del evento</label>
                    <input
                        type="text" id="title" name="title"
                        value="{{ old('title') }}"
                        placeholder="Ej: La Traviata — Ópera en dos actos"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('title') ? 'border-red-400' : '' }}"
                        required
                    >
                    @error('title') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="description">Descripción</label>
                    <textarea
                        id="description" name="description"
                        rows="3"
                        placeholder="Describe el espectáculo, artistas, duración..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition resize-none {{ $errors->has('description') ? 'border-red-400' : '' }}"
                    >{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fecha y hora --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="event_date">Fecha del evento</label>
                        <input
                            type="date" id="event_date" name="event_date"
                            value="{{ old('event_date') }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('event_date') ? 'border-red-400' : '' }}"
                            required
                        >
                        @error('event_date') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="event_time">Hora</label>
                        <input
                            type="time" id="event_time" name="event_time"
                            value="{{ old('event_time') }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('event_time') ? 'border-red-400' : '' }}"
                            required
                        >
                        @error('event_time') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Venue y capacidad --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="venue">Sala / Escenario</label>
                        <input
                            type="text" id="venue" name="venue"
                            value="{{ old('venue') }}"
                            placeholder="Ej: Sala principal"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="capacity">Capacidad total</label>
                        <input
                            type="number" id="capacity" name="capacity"
                            value="{{ old('capacity') }}"
                            placeholder="Ej: 450"
                            min="1"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('capacity') ? 'border-red-400' : '' }}"
                            required
                        >
                        @error('capacity') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Venta de boletas --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#074799]/08 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#074799]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V9z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-800">Venta de boletas</h2>
                    <p class="text-xs text-gray-400">Precios y ventana de venta</p>
                </div>
            </div>

            <div class="p-6 flex flex-col gap-5">

                {{-- Precio base --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="base_price">Precio base (COP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input
                            type="number" id="base_price" name="base_price"
                            value="{{ old('base_price') }}"
                            placeholder="0"
                            min="0"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-7 pr-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('base_price') ? 'border-red-400' : '' }}"
                            required
                        >
                    </div>
                    @error('base_price') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Apertura y cierre de venta --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="sale_start">Apertura de venta</label>
                        <input
                            type="datetime-local" id="sale_start" name="sale_start"
                            value="{{ old('sale_start') }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('sale_start') ? 'border-red-400' : '' }}"
                            required
                        >
                        @error('sale_start') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5" for="sale_end">Cierre de venta</label>
                        <input
                            type="datetime-local" id="sale_end" name="sale_end"
                            value="{{ old('sale_end') }}"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('sale_end') ? 'border-red-400' : '' }}"
                            required
                        >
                        @error('sale_end') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Póster --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#001A6E]/06 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#001A6E]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-800">Póster del evento</h2>
                    <p class="text-xs text-gray-400">JPG, PNG o WEBP — máx. 2MB</p>
                </div>
            </div>

            <div class="p-6">
                <label for="poster"
                       class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-[#009990] hover:bg-[#009990]/03 transition"
                       id="poster-label">
                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                    <p class="text-xs text-gray-400">Haz clic para subir o arrastra la imagen</p>
                    <p id="poster-name" class="text-xs text-[#009990] mt-1 hidden"></p>
                    <input type="file" id="poster" name="poster" accept="image/*" class="hidden">
                </label>
                @error('poster') <p class="text-xs text-red-400 mt-2">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Estado --}}
        <div class="bg-white border border-gray-100 rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Publicar evento</p>
                <p class="text-xs text-gray-400 mt-0.5">Visible en la cartelera pública al guardar</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="status" value="draft">
                <input type="checkbox" name="status" value="active" {{ old('status') === 'active' ? 'checked' : '' }} class="sr-only peer">
                <div class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-[#009990] transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition peer-checked:after:translate-x-5"></div>
            </label>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('events.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 px-4 py-2 rounded-lg hover:bg-white transition">
                Cancelar
            </a>
            <button type="submit"
                    class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-sm font-medium px-5 py-2 rounded-lg transition">
                Guardar evento
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.getElementById('poster').addEventListener('change', function() {
    const name = this.files[0]?.name;
    if (name) {
        document.getElementById('poster-name').textContent = name;
        document.getElementById('poster-name').classList.remove('hidden');
    }
});
</script>
@endpush

@endsection