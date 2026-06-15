@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('content')
    <div class="state-denied" role="alert" aria-live="assertive">
        <div class="state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
        <h1 class="state-title">Acceso denegado</h1>
        <p class="state-description">No tienes el permiso necesario para acceder a esta sección del sistema. Si crees que esto es un error, contacta al administrador.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Volver al Dashboard
        </a>
    </div>
@endsection
