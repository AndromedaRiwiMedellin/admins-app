<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Orbix Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=Syne:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #001A6E;
            color: #f0f4ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrap { width: 100%; max-width: 400px; }

        .login-logo {
            text-align: center;
            margin-bottom: 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .login-logo svg { width: 56px; height: 56px; }

        .login-logo h1 {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
            color: #f0f4ff;
        }

        .login-logo p { font-size: 13px; color: rgba(255,255,255,0.55); }

        .login-card {
            background: rgba(0,0,0,0.25);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 16px;
            padding: 32px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.55);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            background: rgba(0,0,0,0.25);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 8px;
            padding: 11px 14px;
            color: #f0f4ff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .15s;
        }

        .form-input:focus { border-color: #009990; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #074799;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
            margin-top: 8px;
        }

        .btn-login:hover { background: #0a5ab8; }

        .error-box {
            background: rgba(255,107,107,0.10);
            border: 1px solid rgba(255,107,107,0.25);
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            color: #ff6b6b;
            margin-bottom: 18px;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(255,255,255,0.30);
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-logo">
            <svg viewBox="0 0 56 56" fill="none">
                <polygon points="28,4 46,14 46,38 28,48 10,38 10,14" fill="#074799" opacity="0.3"/>
                <polygon points="28,10 42,18 42,34 28,42 14,34 14,18" fill="#009990" opacity="0.7"/>
                <polygon points="28,18 35,22 35,32 28,36 21,32 21,22" fill="none" stroke="#009990" stroke-width="2"/>
                <circle cx="28" cy="27" r="4" fill="#009990"/>
                <path d="M37,8 L48,13 L48,25 C48,35 42,41 37,44 C32,41 26,35 26,25 L26,13 Z" fill="#001A6E" stroke="#E1FFBB" stroke-width="1.5"/>
                <text x="37" y="30" text-anchor="middle" font-family="system-ui" font-size="10" font-weight="700" fill="#E1FFBB">A</text>
            </svg>
            <h1>Orbix</h1>
            <p>Panel de administración</p>
        </div>

        <div class="login-card">
            @if($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>
        </div>

        <div class="login-footer">
            Orbix Admin © {{ date('Y') }} — Solo personal autorizado
        </div>
    </div>
</body>
</html>