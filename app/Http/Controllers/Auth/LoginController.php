<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\CognitoAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = strtoupper($request->input('email'));
        $password = $request->input('password');

        $cognito = new CognitoAuthService();
        $result = $cognito->authenticate($username, $password);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['error']])->withInput();
        }

        $sub = $result['sub'];

        $user = !empty($sub) ? User::where('cognito_sub', $sub)->first() : null;
        $user = $user ?? User::where('email', $username)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no registrado en el sistema'])->withInput();
        }

        if ($user->status !== 'ACTIVO') {
            return back()->withErrors(['email' => 'Usuario no está activo'])->withInput();
        }

        if (empty($user->cognito_sub)) {
            $user->cognito_sub = $sub;
            $user->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
