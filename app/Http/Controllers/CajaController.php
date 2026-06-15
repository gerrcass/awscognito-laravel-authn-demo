<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        return view('caja.index');
    }

    public function open(Request $request)
    {
        return redirect('/caja')->with('status', 'Caja abierta correctamente.');
    }

    public function close(Request $request)
    {
        return redirect('/caja')->with('status', 'Caja cerrada correctamente.');
    }
}
