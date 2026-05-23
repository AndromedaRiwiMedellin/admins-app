<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Orbix Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-5">

<div class="flex w-full max-w-3xl min-h-[480px] rounded-2xl overflow-hidden shadow-lg">

    {{-- Panel izquierdo --}}
    <div class="w-[42%] bg-[#009990] flex flex-col items-center justify-center gap-5 p-12">
        <div class="w-16 h-16 bg-[#001A6E]/25 rounded-2xl flex items-center justify-center">
            <svg class="w-9 h-9" viewBox="0 0 56 56" fill="none">
                <polygon points="28,4 46,14 46,38 28,48 10,38 10,14" fill="#001A6E" opacity="0.3"/>
                <polygon points="28,10 42,18 42,34 28,42 14,34 14,18" fill="#E1FFBB" opacity="0.7"/>
                <polygon points="28,18 35,22 35,32 28,36 21,32 21,22" fill="none" stroke="#E1FFBB" stroke-width="2"/>
                <circle cx="28" cy="27" r="4" fill="#E1FFBB"/>
                <path d="M37,8 L48,13 L48,25 C48,35 42,41 37,44 C32,41 26,35 26,25 L26,13 Z" fill="#009990" stroke="#E1FFBB" stroke-width="1.5"/>
                <text x="37" y="30" text-anchor="middle" font-family="system-ui" font-size="10" font-weight="700" fill="#E1FFBB">A</text>
            </svg>
        </div>
        <div class="text-center">
            <p class="text-2xl font-medium text-[#E1FFBB] tracking-tight">Andromeda</p>
            <p class="text-xs text-[#E1FFBB]/55 mt-1">Panel de administración</p>
        </div>
        <div class="w-8 h-px bg-[#E1FFBB]/20"></div>
        <p class="text-xs text-[#E1FFBB]/45 text-center leading-relaxed max-w-[170px]">
            Solo personal autorizado puede acceder a este sistema
        </p>
    </div>

    {{-- Panel derecho --}}
    <div class="flex-1 bg-white flex items-center justify-center p-10">
        <div class="w-full max-w-[300px]">

            <span class="inline-flex items-center gap-1.5 bg-[#009990]/08 text-[#009990] text-xs rounded-full px-3 py-1 mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#009990]"></span>
                Acceso seguro
            </span>

            <h1 class="text-lg font-medium text-[#001A6E] mb-1">Bienvenido de vuelta</h1>
            <p class="text-sm text-gray-400 mb-7">Ingresa tus credenciales para continuar</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2.5 mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="email">Correo electrónico</label>
                    <input
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('email') ? 'border-red-400' : '' }}"
                        type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@teatro.com"
                        autocomplete="email" autofocus required
                    >
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5" for="password">Contraseña</label>
                    <input
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-[#009990] focus:bg-white transition {{ $errors->has('password') ? 'border-red-400' : '' }}"
                        type="password" id="password" name="password"
                        placeholder="••••••••"
                        autocomplete="current-password" required
                    >
                </div>

                <button type="submit"
                    class="w-full bg-[#009990] hover:bg-[#007a74] text-[#E1FFBB] font-medium text-sm rounded-lg py-2.5 transition active:scale-[0.99]">
                    Ingresar
                </button>
            </form>

            <p class="text-center text-xs text-gray-300 mt-7">
                Orbix Admin &copy; {{ date('Y') }} &mdash; Solo personal autorizado
            </p>
        </div>
    </div>

</div>

</body>
</html>