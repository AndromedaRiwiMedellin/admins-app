@extends('layouts.admin')
@section('title', 'Editar empleado')

@section('topbar-actions')
    <a href="{{ route('employees.index') }}"
       class="text-xs text-[#E1FFBB]/70 hover:text-[#E1FFBB] transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Volver
    </a>
@endsection

@section('content')

<div class="max-w-2xl mx-auto flex flex-col gap-4">

    {{-- Formulario principal --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-medium text-[#E1FFBB] flex-shrink-0"
                 style="background: {{ $employee->can_tickets && $employee->can_access ? '#001A6E' : ($employee->can_tickets ? '#009990' : '#074799') }}">
                {{ strtoupper(substr($employee->full_name ?? $employee->email, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-sm font-medium text-gray-800">{{ $employee->full_name ?? $employee->email }}</h2>
                <p class="text-xs text-gray-400">Editando información y permisos</p>
            </div>
            <div class="ml-auto">
                @if($employee->is_active)
                    <span class="text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded-full">Activo</span>
                @else
                    <span class="text-[10px] font-medium bg-gray-100 text-gray-400 border border-gray-200 px-2 py-0.5 rounded-full">Inactivo</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('employees.update', $employee) }}" class="p-6 flex flex-col gap-5">
            @csrf
            @method('PUT')

            {{-- Nombre y apellido --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="first_name">Nombre</label>
                    <input
                        type="text" id="first_name" name="first_name"
                        value="{{ old('first_name', explode(' ', $employee->user->full_name ?? '')[0] ?? '') }}"
                        placeholder="Ej: María"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('first_name') ? 'border-red-400' : '' }}"
                        required
                    >
                    @error('first_name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="last_name">Apellido</label>
                    <input
                        type="text" id="last_name" name="last_name"
                        value="{{ old('last_name', implode(' ', array_slice(explode(' ', $employee->user->full_name ?? ''), 1))) }}"
                        placeholder="Ej: Restrepo"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('last_name') ? 'border-red-400' : '' }}"
                        required
                    >
                    @error('last_name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5" for="email">Correo electrónico</label>
                <input
                    type="email" id="email" name="email"
                    value="{{ old('email', $employee->user->email ?? '') }}"
                    placeholder="empleado@teatro.com"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('email') ? 'border-red-400' : '' }}"
                    required
                >
                @error('email')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5" for="password">
                    Nueva contraseña <span class="text-gray-300">(dejar vacío para no cambiar)</span>
                </label>
                <input
                    type="password" id="password" name="password"
                    placeholder="Mínimo 8 caracteres"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('password') ? 'border-red-400' : '' }}"
                >
                @error('password')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5" for="phone">
                    Teléfono <span class="text-gray-300">(opcional)</span>
                </label>
                <input
                    type="text" id="phone" name="phone"
                    value="{{ old('phone', $employee->user->phone ?? '') }}"
                    placeholder="+57 300 000 0000"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 outline-none focus:border-[#009990] focus:bg-white transition"
                >
            </div>

            {{-- Permisos --}}
            <div>
                <p class="text-xs font-medium text-gray-600 mb-3">Permisos de acceso</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#009990]/40 transition has-[:checked]:border-[#009990] has-[:checked]:bg-[#009990]/05">
                        <input type="checkbox" name="can_tickets" value="1"
                               {{ old('can_tickets', $employee->can_tickets) ? 'checked' : '' }}
                               class="mt-0.5 accent-[#009990]">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Portal Tickets</p>
                            <p class="text-xs text-gray-400 mt-0.5">Venta presencial en taquilla</p>
                            <p class="text-[10px] text-[#009990] mt-1">tickets.andromeda...</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-[#074799]/40 transition has-[:checked]:border-[#074799] has-[:checked]:bg-[#074799]/05">
                        <input type="checkbox" name="can_access" value="1"
                               {{ old('can_access', $employee->can_access) ? 'checked' : '' }}
                               class="mt-0.5 accent-[#074799]">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Portal Acceso</p>
                            <p class="text-xs text-gray-400 mt-0.5">Control de entrada al teatro</p>
                            <p class="text-[10px] text-[#074799] mt-1">acceso.andromeda...</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Estado --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-gray-700">Empleado activo</p>
                    <p class="text-xs text-gray-400 mt-0.5">Puede iniciar sesión en los portales asignados</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-[#009990] transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('employees.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-sm font-medium px-5 py-2 rounded-lg transition">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>

    {{-- Zona de peligro FUERA del form principal --}}
    <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
        <p class="text-xs font-medium text-red-600 mb-1">Zona de peligro</p>
        <p class="text-xs text-red-400 mb-3">Esta acción es irreversible. Se eliminarán todos los datos del empleado.</p>
        <form method="POST" action="{{ route('employees.destroy', $employee) }}"
              onsubmit="return confirm('¿Estás seguro? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-xs text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition">
                Eliminar empleado
            </button>
        </form>
    </div>

</div>

@endsection