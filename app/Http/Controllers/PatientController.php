<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        \Auth::user()->load('role.permissions');
        if (!\Auth::user()->hasPerm('patients.view')) {
            return response()->view('acceso-denegado', ['permission' => 'patients.view'], 403);
        }
        return view('patients.index');
    }
}
