@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('content')
    <h1>Acceso denegado</h1>
    <p>No tienes permiso para acceder a esta sección.</p>
    <a href="{{ route('dashboard') }}">Volver al Dashboard</a>
@endsection
