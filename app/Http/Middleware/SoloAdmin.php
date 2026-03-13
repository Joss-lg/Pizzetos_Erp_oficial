<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no está logueado o su cargo NO es 1 (Administrador)
        if (!Auth::check() || Auth::user()->id_ca != 1) {
            return redirect('/home')->with('error', 'No tienes permisos de Administrador para entrar aquí.');
        }

        return $next($request);
    }
}