@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="login-card-header">
            <div class="brand">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.5rem;height:1.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                MediCore
            </div>
            <h1>Iniciar sesión</h1>
            <p>Ingresa tus credenciales para acceder al sistema.</p>
        </div>

        <div class="login-card-body">
            @if($errors->any())
                <div class="alert alert-error" role="alert" aria-live="assertive">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <div class="alert-content">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="/login" novalidate>
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label is-required">Usuario</label>
                    <input
                        type="text"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-input"
                        placeholder="Ej: ADMIN, ENFERMERA, CAJERO"
                        required
                        autocomplete="username"
                        autofocus
                        aria-describedby="email-help"
                    >
                    <p id="email-help" style="font-size:0.8125rem;color:var(--color-text-muted);margin-top:var(--space-2);">Use su nombre de usuario en mayúsculas.</p>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label is-required">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        placeholder="Contraseña"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    Ingresar
                </button>
            </form>
        </div>

        <div class="login-card-footer">
            Sistema de demostración — AWS Cognito + Laravel
        </div>
    </div>
</div>
@endsection
