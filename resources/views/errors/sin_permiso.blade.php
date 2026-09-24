<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin acceso — Pizzetos</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: #f8f8f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            padding: 24px;
            color: #1a1a1a;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
        }
        .logo {
            height: 40px;
            margin-bottom: 32px;
        }
        .icon {
            width: 64px;
            height: 64px;
            background: #fff4f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        h1 {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }
        p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn-logout {
            display: inline-block;
            background: #e65c00;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 32px;
            border-radius: 100px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-logout:hover { background: #c94f00; }
        .user-info {
            margin-bottom: 24px;
            font-size: 13px;
            color: #999;
        }
        .user-info strong { color: #333; }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos" class="logo">

        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                 fill="none" stroke="#e65c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <h1>Sin acceso</h1>

        @if(session('error'))
            <p>{{ session('error') }}</p>
        @else
            <p>Tu cuenta no tiene permisos habilitados.<br>Pide al administrador que configure tu acceso.</p>
        @endif

        @auth
            <div class="user-info">
                Sesión activa: <strong>{{ auth()->user()->nickName }}</strong>
            </div>
        @endauth

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>