<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $credentials = $request->validate([
            'nickName' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['nickName' => $credentials['nickName'], 'password' => $credentials['password'], 'status' => 1])) {
            $request->session()->regenerate();
            
            if (Auth::user()->id_ca == 1) {
                return redirect()->intended('dashboard');
            } else {
                return redirect()->intended('venta/flujo-caja');
            }
        }

        return back()->withErrors([
            'nickName' => 'Las credenciales no coinciden o el usuario está inactivo.',
        ])->onlyInput('nickName');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}