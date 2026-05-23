@extends('layouts.admin')
@section('title', 'Empleados')

@section('topbar-actions')
    <a href="{{ route('employees.create') }}"
       class="bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] text-xs font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo empleado
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Total empleados</p>
        <p class="text-2xl font-medium text-[#001A6E]">{{ $employees->total() }}</p>
        <p class="text-xs text-gray-400 mt-1">Registrados</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Acceso a Tickets</p>
        <p class="text-2xl font-medium text-[#009990]">{{ $employees->where('can_tickets', true)->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">Portal de taquilla</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4">
        <p class="text-xs text-gray-400 mb-2">Acceso a Acceso</p>
        <p class="text-2xl font-medium text-[#074799]">{{ $employees->where('can_access', true)->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">Portal de entrada</p>
    </div>
</div>

{{-- Tabla --}}
<div class="bg-white border border-gray-100 rounded-xl overflow-hidden">

    {{-- Buscador --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-sm font-medium text-gray-800">Lista de empleados</h2>
        <form method="GET" action="{{ route('employees.index') }}" class="flex items-center gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Buscar por nombre o correo..."
                class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-700 outline-none focus:border-[#009990] w-56 transition"
            >
            <button type="submit"
                class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg transition">
                Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('employees.index') }}"
                   class="text-xs text-gray-400 hover:text-gray-600 transition">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- Lista --}}
    @forelse($employees as $employee)
        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">

            {{-- Avatar --}}
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-medium text-[#E1FFBB] flex-shrink-0"
                 style="background: {{ $employee->can_tickets && $employee->can_access ? '#001A6E' : ($employee->can_tickets ? '#009990' : '#074799') }}">
                {{ strtoupper(substr($employee->full_name ?? $employee->email, 0, 2)) }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $employee->full_name ?? '—' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $employee->email }}</p>
            </div>

            {{-- Permisos --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($employee->can_tickets)
                    <span class="text-[10px] font-medium bg-[#009990]/10 text-[#007a74] border border-[#009990]/20 px-2 py-0.5 rounded-full">
                        Tickets
                    </span>
                @endif
                @if($employee->can_access)
                    <span class="text-[10px] font-medium bg-[#001A6E]/08 text-[#001A6E] border border-[#001A6E]/15 px-2 py-0.5 rounded-full">
                        Acceso
                    </span>
                @endif
                @if(!$employee->can_tickets && !$employee->can_access)
                    <span class="text-[10px] text-gray-400 border border-gray-200 px-2 py-0.5 rounded-full">
                        Sin permisos
                    </span>
                @endif
            </div>

            {{-- Estado --}}
            <div class="flex-shrink-0">
                @if($employee->is_active)
                    <span class="text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded-full">
                        Activo
                    </span>
                @else
                    <span class="text-[10px] font-medium bg-gray-100 text-gray-400 border border-gray-200 px-2 py-0.5 rounded-full">
                        Inactivo
                    </span>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('employees.edit', $employee) }}"
                   class="text-xs text-[#074799] hover:text-[#001A6E] border border-[#074799]/20 hover:border-[#001A6E]/30 px-2.5 py-1 rounded-lg transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                      onsubmit="return confirm('¿Eliminar a {{ $employee->full_name }}?')">
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
            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <p class="text-sm text-gray-400">No hay empleados registrados</p>
            <a href="{{ route('employees.create') }}" class="mt-3 text-xs text-[#009990] hover:underline">Agregar el primero →</a>
        </div>
    @endforelse

    {{-- Paginación --}}
    @if($employees->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $employees->links() }}
        </div>
    @endif
</div>

@endsection