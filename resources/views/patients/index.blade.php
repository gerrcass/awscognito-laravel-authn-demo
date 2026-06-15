@extends('layouts.app')

@section('title', 'Pacientes')

@section('content')
    <div class="card-header">
        <h1>Pacientes</h1>
        <p>Listado de pacientes registrados en el sistema.</p>
    </div>

    <div class="card-body is-padded-sm">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Edad</th>
                        <th scope="col">Diagnóstico</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de ingreso</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1001</td>
                        <td><strong>Juan Carlos Pérez</strong></td>
                        <td>45</td>
                        <td>Hipertensión arterial</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td>2024-01-15</td>
                    </tr>
                    <tr>
                        <td>1002</td>
                        <td><strong>María Elena Rodríguez</strong></td>
                        <td>62</td>
                        <td>Diabetes tipo 2</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td>2024-02-20</td>
                    </tr>
                    <tr>
                        <td>1003</td>
                        <td><strong>Pedro Antonio Gómez</strong></td>
                        <td>33</td>
                        <td>Gripe común</td>
                        <td><span class="badge badge-warning">Alta</span></td>
                        <td>2024-03-10</td>
                    </tr>
                    <tr>
                        <td>1004</td>
                        <td><strong>Ana María López</strong></td>
                        <td>28</td>
                        <td>Asma bronquial</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td>2024-03-25</td>
                    </tr>
                    <tr>
                        <td>1005</td>
                        <td><strong>Roberto Carlos Martínez</strong></td>
                        <td>55</td>
                        <td>Artritis reumatoide</td>
                        <td><span class="badge badge-success">Activo</span></td>
                        <td>2024-04-05</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
