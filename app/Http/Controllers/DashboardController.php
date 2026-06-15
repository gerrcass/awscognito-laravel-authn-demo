<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('dashboard.view')) {
            return response()->view('acceso-denegado', ['permission' => 'dashboard.view'], 403);
        }
        return view('dashboard');
    }
}
