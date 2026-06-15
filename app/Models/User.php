<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'configuracion.users';

    protected $fillable = [
        'name', 'email', 'correo', 'password', 'cognito_sub', 'role_user', 'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_user');
    }

    public function hasPerm(string $name): bool
    {
        return $this->role?->permissions->contains('name', $name) ?? false;
    }
}
