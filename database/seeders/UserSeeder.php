<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'ADMIN', 'name' => 'Administrador', 'role' => 'admin', 'status' => 'ACTIVO'],
            ['email' => 'ENFERMERA', 'name' => 'Enfermera Demo', 'role' => 'enfermera', 'status' => 'ACTIVO'],
            ['email' => 'CAJERO', 'name' => 'Cajero Demo', 'role' => 'cajero', 'status' => 'ACTIVO'],
            ['email' => 'INACTIVO', 'name' => 'Usuario Inactivo', 'role' => 'cajero', 'status' => 'INACTIVO'],
        ];

        foreach ($users as $data) {
            $role = Role::where('name', $data['role'])->first();
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role_user' => $role?->id,
                    'status' => $data['status'],
                ]
            );
        }
    }
}
