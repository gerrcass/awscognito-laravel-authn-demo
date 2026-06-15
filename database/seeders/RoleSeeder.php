<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'enfermera',
            'cajero',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        $permissions = [
            'dashboard.view',
            'users.manage',
            'caja.open',
            'caja.close',
            'patients.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync(Permission::all()->pluck('id'));

        $enfermera = Role::where('name', 'enfermera')->first();
        $enfermera->permissions()->sync(
            Permission::whereIn('name', ['dashboard.view', 'patients.view'])->pluck('id')
        );

        $cajero = Role::where('name', 'cajero')->first();
        $cajero->permissions()->sync(
            Permission::whereIn('name', ['dashboard.view', 'caja.open', 'caja.close'])->pluck('id')
        );
    }
}
