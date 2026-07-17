<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermisoModulo
{
    /**
     * Uso en rutas: ->middleware('permiso:productos,gestionar')
     * $modulo debe existir en config/modulos.php y $accion debe ser una de:
     * mostrar, crear, editar, eliminar, gestionar.
     */
    public function handle(Request $request, Closure $next, string $modulo, string $accion)
    {
        $user = Auth::user();

        if (!$user || !$user->tienePermiso($modulo, $accion)) {
            return redirect('/venta/flujo-caja')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}