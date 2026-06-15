@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="card-header">
        <h1>Usuarios</h1>
        <p>Listado de usuarios registrados en el sistema con sus roles y estados.</p>
    </div>

    <div class="card-body is-padded-sm">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Cognito Sub</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->correo)
                                    <br><span style="font-size:0.8125rem;color:var(--color-text-muted);">{{ $user->correo }}</span>
                                @endif
                            </td>
                            <td><code style="font-family:var(--font-mono);font-size:0.8125rem;background:var(--color-background);padding:var(--space-1) var(--space-2);border-radius:var(--radius-sm);">{{ $user->email }}</code></td>
                            <td>
                                <span class="badge badge-info">{{ $user->role?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($user->status === 'ACTIVO')
                                    <span class="badge badge-success">{{ $user->status }}</span>
                                @else
                                    <span class="badge badge-error">{{ $user->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->cognito_sub)
                                    <code style="font-family:var(--font-mono);font-size:0.75rem;background:var(--color-background);padding:var(--space-1) var(--space-2);border-radius:var(--radius-sm);">{{ $user->cognito_sub }}</code>
                                @else
                                    <span style="font-size:0.8125rem;color:var(--color-text-muted);">No vinculado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:var(--space-8);color:var(--color-text-muted);">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
