@extends('layouts.app')

@section('title', 'Caja')

@section('content')
    <h1>Caja</h1>

    <form action="{{ route('caja.open') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Abrir caja</button>
    </form>

    <form action="{{ route('caja.close') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Cerrar caja</button>
    </form>
@endsection
