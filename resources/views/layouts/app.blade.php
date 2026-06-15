<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediCore')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    @auth
        <div class="nav">
            <span>Bienvenido, {{ Auth::user()->name }}</span>
            @if(Auth::user()->hasPerm('dashboard.view'))
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @endif
            @if(Auth::user()->hasPerm('users.manage'))
                <a href="{{ route('admin.users') }}">Usuarios</a>
            @endif
            @if(Auth::user()->hasPerm('patients.view'))
                <a href="{{ route('patients') }}">Pacientes</a>
            @endif
            @if(Auth::user()->hasPerm('dashboard.view'))
                <a href="{{ route('caja') }}">Caja</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    @endauth

    @if(session('status'))
        <div class="success">{{ session('status') }}</div>
    @endif

    @yield('content')
</body>
</html>
