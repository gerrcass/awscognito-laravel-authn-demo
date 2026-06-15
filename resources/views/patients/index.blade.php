@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')
    <h1>Pacientes</h1>
    <p>Listado de pacientes registrados en el sistema.</p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Diagnóstico</th>
                <th>Estado</th>
                <th>Fecha de ingreso</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1001</td>
                <td>Juan Carlos Pérez</td>
                <td>45</td>
                <td>Hipertensión arterial</td>
                <td>Activo</td>
                <td>2024-01-15</td>
            </tr>
            <tr>
                <td>1002</td>
                <td>María Elena Rodríguez</td>
                <td>62</td>
                <td>Diabetes tipo 2</td>
                <td>Activo</td>
                <td>2024-02-20</td>
            </tr>
            <tr>
                <td>1003</td>
                <td>Pedro Antonio Gómez</td>
                <td>33</td>
                <td>Gripe común</td>
                <td>Alta</td>
                <td>2024-03-10</td>
            </tr>
            <tr>
                <td>1004</td>
                <td>Ana María López</td>
                <td>28</td>
                <td>Asma bronquial</td>
                <td>Activo</td>
                <td>2024-03-25</td>
            </tr>
            <tr>
                <td>1005</td>
                <td>Roberto Carlos Martínez</td>
                <td>55</td>
                <td>Artritis reumatoide</td>
                <td>Activo</td>
                <td>2024-04-05</td>
            </tr>
        </tbody>
    </table>
@endsection
