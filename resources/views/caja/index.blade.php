@extends('layouts.app')

@section('title', 'Caja')

@section('content')
    <div class="card-header">
        <h1>Caja</h1>
        <p>Operaciones de apertura y cierre de caja.</p>
    </div>

    <div class="card-body">
        <div class="action-bar">
            <form action="{{ route('caja.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    Abrir caja
                </button>
            </form>

            <form action="{{ route('caja.close') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Cerrar caja
                </button>
            </form>
        </div>
    </div>
@endsection
