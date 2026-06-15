<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('dashboard.view')) {
            return response()->view('acceso-denegado', ['permission' => 'dashboard.view'], 403);
        }
        return view('caja.index');
    }

    public function open(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('caja.open')) {
            return response()->view('acceso-denegado', ['permission' => 'caja.open'], 403);
        }
        return redirect('/caja')->with('status', 'Caja abierta correctamente.');
    }

    public function close(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('caja.close')) {
            return response()->view('acceso-denegado', ['permission' => 'caja.close'], 403);
        }
        return redirect('/caja')->with('status', 'Caja cerrada correctamente.');
    }
}
