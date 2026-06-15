@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card-header">
        <h1>Dashboard</h1>
        <p>Bienvenido, {{ Auth::user()->name }}. Has iniciado sesión correctamente en el sistema.</p>
    </div>

    <div class="card-body">
        <div class="dashboard-grid">
            @if(Auth::user()->hasPerm('users.manage'))
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    </div>
                    <h3>Usuarios</h3>
                    <p>Administra los usuarios del sistema, sus roles y estados.</p>
                    <a href="{{ route('admin.users') }}">Ver usuarios →</a>
                </div>
            @endif

            @if(Auth::user()->hasPerm('patients.view'))
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    </div>
                    <h3>Pacientes</h3>
                    <p>Consulta el registro de pacientes y sus diagnósticos.</p>
                    <a href="{{ route('patients') }}">Ver pacientes →</a>
                </div>
            @endif

            @if(Auth::user()->hasPerm('dashboard.view'))
                <div class="dashboard-card">
                    <div class="dashboard-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3>Caja</h3>
                    <p>Operaciones de apertura y cierre de caja.</p>
                    <a href="{{ route('caja') }}">Ir a caja →</a>
                </div>
            @endif
        </div>
    </div>
@endsection
