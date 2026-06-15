<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('users.manage')) {
            return response()->view('acceso-denegado', ['permission' => 'users.manage'], 403);
        }
        $users = User::with('role')->get();
        return view('admin.users.index', compact('users'));
    }
}
